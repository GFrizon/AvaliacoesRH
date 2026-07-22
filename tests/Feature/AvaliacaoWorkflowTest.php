<?php

namespace Tests\Feature;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\FormularioTipo;
use App\Enums\PerguntaTipo;
use App\Enums\UserRole;
use App\Mail\AvaliacaoPendenteMail;
use App\Mail\AvaliacaoConcluidaMail;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Pergunta;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AvaliacaoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_due_evaluation_email_and_marks_as_pending(): void
    {
        Mail::fake();

        [$avaliacao, $gestor] = $this->makeAvaliacao([
            'status' => AvaliacaoStatus::Agendada,
            'data_limite' => now()->subDay(),
        ]);

        $this->artisan('avaliacoes:enviar-pendentes')
            ->expectsOutput('E-mails enviados: 1')
            ->assertSuccessful();

        Mail::assertSent(AvaliacaoPendenteMail::class, fn ($mail) => $mail->hasTo($gestor->email));
        $avaliacao->refresh();

        $this->assertSame(AvaliacaoStatus::Pendente, $avaliacao->status);
        $this->assertNotNull($avaliacao->notificado_em);
        $this->assertDatabaseHas('email_logs', [
            'avaliacao_id' => $avaliacao->id,
            'destinatario' => $gestor->email,
            'tipo' => 'avaliacao_pendente',
            'status' => 'enviado',
        ]);
    }

    public function test_command_ignores_due_evaluations_from_inactive_colaboradores(): void
    {
        Mail::fake();

        [$avaliacao, $gestor, $pergunta, $colaborador] = $this->makeAvaliacao([
            'status' => AvaliacaoStatus::Agendada,
            'data_limite' => now()->subDay(),
        ]);
        $colaborador->update(['is_active' => false]);

        $this->artisan('avaliacoes:enviar-pendentes')
            ->expectsOutput('E-mails enviados: 0')
            ->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertSame(AvaliacaoStatus::Agendada, $avaliacao->refresh()->status);
    }

    public function test_not_effective_result_cancels_future_evaluations(): void
    {
        Mail::fake();

        [$avaliacao, $gestor, $pergunta, $colaborador, $formulario, $empresa] = $this->makeAvaliacao([
            'status' => AvaliacaoStatus::Pendente,
            'data_limite' => now(),
        ]);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);

        $futura = Avaliacao::create([
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaborador->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::SeisMeses,
            'status' => AvaliacaoStatus::Agendada,
            'data_limite' => now()->addMonths(3),
        ]);

        $this->actingAs($gestor)
            ->post(route('avaliacoes.submit', $avaliacao), [
                'respostas' => [$pergunta->id => 'Nao atende ao esperado.'],
                'efetivar' => '0',
            ])
            ->assertRedirect(route('avaliacoes.index'));

        $this->assertSame(AvaliacaoStatus::Concluida, $avaliacao->refresh()->status);
        $this->assertFalse($avaliacao->efetivar);
        $this->assertSame(AvaliacaoStatus::Cancelada, $futura->refresh()->status);
        Mail::assertSent(AvaliacaoConcluidaMail::class, fn ($mail) => $mail->hasTo($rh->email));
    }

    public function test_rh_users_receive_email_when_evaluation_is_submitted(): void
    {
        Mail::fake();

        [$avaliacao, $gestor, $pergunta, $colaborador, $formulario, $empresa] = $this->makeAvaliacao([
            'status' => AvaliacaoStatus::Pendente,
            'data_limite' => now(),
        ]);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $outroRh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $rhInativo = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
            'is_active' => false,
        ]);

        $this->actingAs($gestor)
            ->post(route('avaliacoes.submit', $avaliacao), [
                'respostas' => [$pergunta->id => 'Entrega dentro do esperado.'],
                'observacoes_finais' => 'Colaborador apto para seguir.',
                'efetivar' => '1',
            ])
            ->assertRedirect(route('avaliacoes.index'));

        $this->assertSame(AvaliacaoStatus::Concluida, $avaliacao->refresh()->status);
        Mail::assertSent(AvaliacaoConcluidaMail::class, fn ($mail) => $mail->hasTo($rh->email));
        Mail::assertSent(AvaliacaoConcluidaMail::class, fn ($mail) => str_contains($mail->render(), 'Colaborador apto para seguir.'));
        Mail::assertSent(AvaliacaoConcluidaMail::class, fn ($mail) => $mail->hasTo($outroRh->email));
        Mail::assertNotSent(AvaliacaoConcluidaMail::class, fn ($mail) => $mail->hasTo($rhInativo->email));
        $this->assertDatabaseHas('email_logs', [
            'avaliacao_id' => $avaliacao->id,
            'destinatario' => $rh->email,
            'tipo' => 'avaliacao_concluida',
            'status' => 'enviado',
        ]);
    }

    private function makeAvaliacao(array $overrides): array
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Operacoes']);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $colaborador = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'nome' => 'Joao Victor',
            'cargo' => 'Operador',
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliacao Industria',
            'tipo' => FormularioTipo::Industria,
        ]);
        $pergunta = Pergunta::create([
            'formulario_id' => $formulario->id,
            'titulo' => 'Pontualidade',
            'tipo' => PerguntaTipo::TextoLongo,
            'ordem' => 1,
        ]);

        $avaliacao = Avaliacao::create([
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaborador->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias,
            ...$overrides,
        ]);

        return [$avaliacao, $gestor, $pergunta, $colaborador, $formulario, $empresa];
    }
}
