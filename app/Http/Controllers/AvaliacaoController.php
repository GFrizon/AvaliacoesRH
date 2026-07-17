<?php

namespace App\Http\Controllers;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\UserRole;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\Formulario;
use App\Models\Resposta;
use App\Models\Setor;
use App\Models\User;
use App\Services\AvaliacaoWorkflowService;
use App\Support\UnidadesNegocio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AvaliacaoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Avaliacao::with(['colaborador.setor', 'gestor', 'formulario']);

        if ($request->user()->isGestor()) {
            $query->where('gestor_id', $request->user()->id);
        } else {
            $query->where('empresa_id', $request->user()->empresa_id);
        }

        $empresaId = $request->user()->empresa_id;

        $query
            ->when($request->filled('busca'), function ($query) use ($request): void {
                $busca = $request->string('busca')->toString();

                $query->whereHas('colaborador', function ($query) use ($busca): void {
                    $query->where('nome', 'like', "%{$busca}%")
                        ->orWhere('cpf', 'like', "%{$busca}%")
                        ->orWhere('cargo', 'like', "%{$busca}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('gestor_id'), fn ($query) => $query->where('gestor_id', $request->integer('gestor_id')))
            ->when($request->filled('ciclo'), fn ($query) => $query->where('ciclo', $request->string('ciclo')->toString()))
            ->when($request->filled('unidade_negocio'), function ($query) use ($request): void {
                $query->whereHas('colaborador', fn ($query) => $query->where('unidade_negocio', $request->string('unidade_negocio')->toString()));
            });

        return view('avaliacoes.index', [
            'avaliacoes' => $query->latest()->paginate(12)->withQueryString(),
            'gestores' => User::where('empresa_id', $empresaId)
                ->where('role', UserRole::Gestor)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'unidadesNegocio' => UnidadesNegocio::options($empresaId),
            'ciclos' => AvaliacaoCiclo::cases(),
            'statusOptions' => AvaliacaoStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isRh(), 403);

        $empresaId = $request->user()->empresa_id;

        return view('avaliacoes.create', [
            'colaboradores' => Colaborador::with('setor')
                ->where('empresa_id', $empresaId)
                ->where('is_active', true)
                ->orderBy('nome')
                ->get(),
            'gestores' => User::where('empresa_id', $empresaId)
                ->where('role', UserRole::Gestor)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'formularios' => Formulario::where('empresa_id', $empresaId)
                ->where('is_active', true)
                ->orderBy('tipo')
                ->get(),
            'ciclos' => AvaliacaoCiclo::cases(),
            'unidadesNegocio' => UnidadesNegocio::options($empresaId),
        ]);
    }

    public function store(Request $request, AvaliacaoWorkflowService $workflow): RedirectResponse
    {
        abort_unless($request->user()->isRh(), 403);

        $empresaId = $request->user()->empresa_id;
        $validated = $request->validate([
            'colaborador_mode' => ['required', Rule::in(['existing', 'new'])],
            'colaborador_id' => [
                'required_if:colaborador_mode,existing',
                'nullable',
                Rule::exists('colaboradores', 'id')->where('empresa_id', $empresaId)->where('is_active', true),
            ],
            'novo_colaborador_nome' => ['required_if:colaborador_mode,new', 'nullable', 'string', 'max:255'],
            'novo_colaborador_cpf' => [
                'required_if:colaborador_mode,new',
                'nullable',
                'string',
                'max:14',
                Rule::unique('colaboradores', 'cpf')->where('empresa_id', $empresaId),
            ],
            'novo_colaborador_unidade_negocio' => [
                'required_if:colaborador_mode,new',
                'nullable',
                'string',
                'max:255',
                Rule::in(UnidadesNegocio::options($empresaId)->all()),
            ],
            'gestor_id' => [
                'required',
                Rule::exists('users', 'id')->where('empresa_id', $empresaId)->where('role', UserRole::Gestor->value)->where('is_active', true),
            ],
            'formulario_id' => [
                'required',
                Rule::exists('formularios', 'id')->where('empresa_id', $empresaId)->where('is_active', true),
            ],
            'ciclo' => ['required', Rule::enum(AvaliacaoCiclo::class)],
            'data_limite' => ['nullable', 'date'],
        ]);

        $colaborador = $validated['colaborador_mode'] === 'new'
            ? $this->criarColaboradorRapido($request, $validated)
            : Colaborador::where('empresa_id', $empresaId)->findOrFail($validated['colaborador_id']);

        $ciclo = AvaliacaoCiclo::from($validated['ciclo']);
        $dataLimite = $validated['data_limite'] ?? $this->dataLimitePadrao($colaborador, $ciclo)->toDateString();

        $avaliacao = Avaliacao::firstOrCreate(
            [
                'colaborador_id' => $colaborador->id,
                'formulario_id' => $validated['formulario_id'],
                'ciclo' => $ciclo->value,
            ],
            [
                'empresa_id' => $empresaId,
                'gestor_id' => $validated['gestor_id'],
                'criada_por' => $request->user()->id,
                'status' => AvaliacaoStatus::Pendente,
                'data_limite' => $dataLimite,
            ],
        );

        if (! $avaliacao->wasRecentlyCreated) {
            return redirect()
                ->route('avaliacoes.show', $avaliacao)
                ->with('status', 'Esta avaliação já existia para este colaborador, modelo e ciclo.');
        }

        $workflow->notificarGestorPendente($avaliacao);

        return redirect()
            ->route('avaliacoes.show', $avaliacao)
            ->with('status', 'Avaliação criada com sucesso.');
    }

    private function criarColaboradorRapido(Request $request, array $validated): Colaborador
    {
        $empresaId = $request->user()->empresa_id;
        $setor = Setor::firstOrCreate(
            ['empresa_id' => $empresaId, 'nome' => 'Não informado'],
            ['descricao' => 'Criado automaticamente para cadastros rápidos de avaliação.'],
        );

        return Colaborador::create([
            'empresa_id' => $empresaId,
            'setor_id' => $setor->id,
            'gestor_id' => $validated['gestor_id'],
            'formulario_id' => $validated['formulario_id'],
            'nome' => $validated['novo_colaborador_nome'],
            'cpf' => $validated['novo_colaborador_cpf'],
            'unidade_negocio' => $validated['novo_colaborador_unidade_negocio'],
            'cargo' => 'Não informado',
            'data_admissao' => now()->toDateString(),
            'is_active' => true,
        ]);
    }

    public function show(Request $request, Avaliacao $avaliacao): View|RedirectResponse
    {
        if ($request->user()->isGestor() && (int) $avaliacao->gestor_id !== (int) $request->user()->id) {
            return redirect()
                ->route('avaliacoes.index')
                ->with('status', 'Essa avaliação não está vinculada ao seu usuário.');
        }

        $this->authorizeAccess($request, $avaliacao);

        $avaliacao->load(['colaborador.setor', 'gestor', 'formulario.perguntas', 'respostas']);

        if ($avaliacao->status === AvaliacaoStatus::Pendente && $request->user()->isGestor() && ! $avaliacao->iniciada_em) {
            $avaliacao->update(['iniciada_em' => now()]);
        }

        return view('avaliacoes.show', compact('avaliacao'));
    }

    public function submit(Request $request, Avaliacao $avaliacao, AvaliacaoWorkflowService $workflow): RedirectResponse
    {
        $this->authorizeAccess($request, $avaliacao);
        abort_unless($request->user()->isGestor(), 403);
        abort_unless($avaliacao->status === AvaliacaoStatus::Pendente, 422);

        $perguntas = $avaliacao->formulario->perguntas->where('is_active', true);
        $validated = $request->validate([
            'respostas' => ['array'],
            'observacoes_finais' => ['nullable', 'string', 'max:5000'],
            'efetivar' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($avaliacao, $perguntas, $validated): void {
            foreach ($perguntas as $pergunta) {
                Resposta::updateOrCreate(
                    ['avaliacao_id' => $avaliacao->id, 'pergunta_id' => $pergunta->id],
                    ['valor' => ['value' => $validated['respostas'][$pergunta->id] ?? null]]
                );
            }

            $avaliacao->update([
                'status' => AvaliacaoStatus::Concluida,
                'observacoes_finais' => $validated['observacoes_finais'] ?? null,
                'efetivar' => $validated['efetivar'],
                'concluida_em' => now(),
            ]);
        });

        $avaliacao->refresh();
        $workflow->cancelarFuturasSeNaoEfetivado($avaliacao);
        $workflow->notificarRhConclusao($avaliacao);

        return redirect()->route('avaliacoes.index')->with('status', 'Avaliação enviada com sucesso.');
    }

    public function reenviarEmail(Request $request, Avaliacao $avaliacao, AvaliacaoWorkflowService $workflow): RedirectResponse
    {
        abort_unless($request->user()->isRh(), 403);
        $this->authorizeAccess($request, $avaliacao);

        $enviados = match ($avaliacao->status) {
            AvaliacaoStatus::Agendada, AvaliacaoStatus::Pendente => $workflow->notificarGestorPendente($avaliacao) ? 1 : 0,
            AvaliacaoStatus::Concluida => $workflow->notificarRhConclusao($avaliacao),
            default => 0,
        };

        if ($enviados === 0) {
            return back()->with('status', 'Nenhum e-mail foi colocado na fila. Verifique se há destinatário ativo com e-mail cadastrado.');
        }

        return back()->with('status', $enviados === 1 ? 'E-mail colocado na fila de envio.' : "{$enviados} e-mails colocados na fila de envio.");
    }

    private function authorizeAccess(Request $request, Avaliacao $avaliacao): void
    {
        $user = $request->user();

        if ($user->isGestor()) {
            abort_unless((int) $avaliacao->gestor_id === (int) $user->id, 403);

            return;
        }

        abort_unless((int) $avaliacao->empresa_id === (int) $user->empresa_id, 403);
    }

    private function dataLimitePadrao(Colaborador $colaborador, AvaliacaoCiclo $ciclo)
    {
        $base = $colaborador->data_admissao ?: now();

        return match ($ciclo) {
            AvaliacaoCiclo::NoventaDias => $base->copy()->addDays(90),
            AvaliacaoCiclo::SeisMeses => $base->copy()->addMonths(6),
            AvaliacaoCiclo::UmAno => $base->copy()->addYear(),
        };
    }
}
