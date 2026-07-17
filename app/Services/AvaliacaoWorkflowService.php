<?php

namespace App\Services;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\UserRole;
use App\Mail\AvaliacaoConcluidaMail;
use App\Mail\AvaliacaoPendenteMail;
use App\Mail\AvaliacoesAgendadasMail;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AvaliacaoWorkflowService
{
    public function garantirAgenda(Colaborador $colaborador): void
    {
        if (! $colaborador->gestor_id || ! $colaborador->formulario_id) {
            return;
        }

        Avaliacao::query()
            ->where('colaborador_id', $colaborador->id)
            ->where('formulario_id', '!=', $colaborador->formulario_id)
            ->where('status', AvaliacaoStatus::Agendada->value)
            ->update([
                'status' => AvaliacaoStatus::Cancelada,
                'cancelada_em' => now(),
                'motivo_cancelamento' => 'Fluxo de avaliação do colaborador foi atualizado pelo RH.',
            ]);

        Avaliacao::query()
            ->where('colaborador_id', $colaborador->id)
            ->where('formulario_id', $colaborador->formulario_id)
            ->whereIn('status', [AvaliacaoStatus::Agendada->value, AvaliacaoStatus::Pendente->value])
            ->update([
                'gestor_id' => $colaborador->gestor_id,
            ]);

        $criadas = new Collection();

        foreach (AvaliacaoCiclo::cases() as $ciclo) {
            $avaliacao = Avaliacao::firstOrCreate(
                [
                    'colaborador_id' => $colaborador->id,
                    'formulario_id' => $colaborador->formulario_id,
                    'ciclo' => $ciclo->value,
                ],
                [
                    'empresa_id' => $colaborador->empresa_id,
                    'gestor_id' => $colaborador->gestor_id,
                    'criada_por' => auth()->id(),
                    'status' => AvaliacaoStatus::Agendada,
                    'data_limite' => $this->dataLimite($colaborador, $ciclo),
                ],
            );

            if ($avaliacao->wasRecentlyCreated) {
                $criadas->push($avaliacao);
            }
        }

        $this->notificarGestorAvaliacoesAgendadas($colaborador, $criadas);
    }

    public function notificarGestorAvaliacoesAgendadas(Colaborador $colaborador, Collection $avaliacoes): bool
    {
        $colaborador->loadMissing(['gestor', 'setor']);

        if ($avaliacoes->isEmpty() || ! $colaborador->gestor?->email) {
            return false;
        }

        return $this->enviarEmail(
            $colaborador->empresa_id,
            $avaliacoes->first()?->id,
            'avaliacoes_agendadas',
            $colaborador->gestor->email,
            "Avaliações agendadas: {$colaborador->nome}",
            new AvaliacoesAgendadasMail($colaborador, $avaliacoes),
        );
    }

    public function enviarPendentesVencidas(): int
    {
        $total = 0;

        Avaliacao::query()
            ->with(['colaborador', 'gestor', 'formulario'])
            ->where('status', AvaliacaoStatus::Agendada->value)
            ->whereDate('data_limite', '<=', now()->toDateString())
            ->whereNull('notificado_em')
            ->chunkById(50, function ($avaliacoes) use (&$total): void {
                foreach ($avaliacoes as $avaliacao) {
                    $total += $this->notificarGestorPendente($avaliacao) ? 1 : 0;
                }
            });

        Avaliacao::query()
            ->with(['colaborador', 'gestor', 'formulario'])
            ->where('status', AvaliacaoStatus::Pendente->value)
            ->whereDate('data_limite', '<=', now()->toDateString())
            ->where(function ($query): void {
                $query->whereNull('ultimo_lembrete_em')
                    ->orWhere('ultimo_lembrete_em', '<=', now()->subDay());
            })
            ->chunkById(50, function ($avaliacoes) use (&$total): void {
                foreach ($avaliacoes as $avaliacao) {
                    if (! $avaliacao->gestor->email) {
                        continue;
                    }

                    $enviado = $this->enviarEmail(
                        $avaliacao->empresa_id,
                        $avaliacao->id,
                        'lembrete_pendente',
                        $avaliacao->gestor->email,
                        "Avaliação pendente: {$avaliacao->colaborador->nome}",
                        new AvaliacaoPendenteMail($avaliacao),
                    );

                    if (! $enviado) {
                        continue;
                    }

                    $avaliacao->update([
                        'ultimo_lembrete_em' => now(),
                        'lembretes_enviados' => $avaliacao->lembretes_enviados + 1,
                    ]);

                    $total++;
                }
            });

        return $total;
    }

    public function notificarGestorPendente(Avaliacao $avaliacao): bool
    {
        $avaliacao->loadMissing(['colaborador', 'gestor', 'formulario']);

        if (! $avaliacao->gestor?->email) {
            return false;
        }

        $enviado = $this->enviarEmail(
            $avaliacao->empresa_id,
            $avaliacao->id,
            'avaliacao_pendente',
            $avaliacao->gestor->email,
            "Avaliação pendente: {$avaliacao->colaborador->nome}",
            new AvaliacaoPendenteMail($avaliacao),
        );

        if (! $enviado) {
            return false;
        }

        $avaliacao->update([
            'status' => AvaliacaoStatus::Pendente,
            'notificado_em' => $avaliacao->notificado_em ?: now(),
            'ultimo_lembrete_em' => now(),
        ]);

        return true;
    }

    public function notificarRhConclusao(Avaliacao $avaliacao): int
    {
        $total = 0;

        User::where('empresa_id', $avaliacao->empresa_id)
            ->where('role', UserRole::Rh)
            ->where('is_active', true)
            ->get()
            ->each(function (User $rh) use ($avaliacao, &$total): void {
                if (! $rh->email) {
                    return;
                }

                $enviado = $this->enviarEmail(
                    $avaliacao->empresa_id,
                    $avaliacao->id,
                    'avaliacao_concluida',
                    $rh->email,
                    "Avaliação concluída: {$avaliacao->colaborador->nome}",
                    new AvaliacaoConcluidaMail($avaliacao),
                );

                if ($enviado) {
                    $total++;
                }
            });

        return $total;
    }

    public function cancelarFuturasSeNaoEfetivado(Avaliacao $avaliacao): int
    {
        if ($avaliacao->efetivar !== false) {
            return 0;
        }

        return Avaliacao::query()
            ->where('colaborador_id', $avaliacao->colaborador_id)
            ->where('id', '!=', $avaliacao->id)
            ->whereIn('status', [AvaliacaoStatus::Agendada->value, AvaliacaoStatus::Pendente->value])
            ->update([
                'status' => AvaliacaoStatus::Cancelada,
                'cancelada_em' => now(),
                'motivo_cancelamento' => 'Colaborador não efetivado em avaliação anterior.',
            ]);
    }

    public function dataLimite(Colaborador $colaborador, AvaliacaoCiclo $ciclo): Carbon
    {
        $base = ($colaborador->data_admissao ?: now())->copy();

        return match ($ciclo) {
            AvaliacaoCiclo::NoventaDias => $base->addDays(90),
            AvaliacaoCiclo::SeisMeses => $base->addMonths(6),
            AvaliacaoCiclo::UmAno => $base->addYear(),
        };
    }

    private function enviarEmail(int $empresaId, ?int $avaliacaoId, string $tipo, string $destinatario, string $assunto, Mailable $mail): bool
    {
        try {
            Mail::to($destinatario)->send($mail);
        } catch (Throwable $exception) {
            $this->registrarEmail($empresaId, $avaliacaoId, $tipo, $destinatario, $assunto, 'falhou', $exception->getMessage());

            return false;
        }

        $this->registrarEmail($empresaId, $avaliacaoId, $tipo, $destinatario, $assunto, 'enviado');

        return true;
    }

    private function registrarEmail(int $empresaId, ?int $avaliacaoId, string $tipo, string $destinatario, string $assunto, string $status, ?string $erro = null): void
    {
        EmailLog::create([
            'empresa_id' => $empresaId,
            'avaliacao_id' => $avaliacaoId,
            'tipo' => $tipo,
            'destinatario' => $destinatario,
            'assunto' => $assunto,
            'status' => $status,
            'enfileirado_em' => now(),
            'erro' => $erro,
        ]);
    }
}
