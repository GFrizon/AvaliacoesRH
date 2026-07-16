<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Colaborador;
use App\Models\Formulario;
use App\Models\Setor;
use App\Models\User;
use App\Services\AvaliacaoWorkflowService;
use App\Support\UnidadesNegocio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ColaboradorController extends Controller
{
    public function index(Request $request): View
    {
        $empresaId = $request->user()->empresa_id;

        $colaboradores = Colaborador::query()
            ->with(['setor', 'gestor', 'formulario'])
            ->where('empresa_id', $empresaId)
            ->when($request->filled('busca'), function ($query) use ($request): void {
                $busca = $request->string('busca')->toString();

                $query->where(function ($query) use ($busca): void {
                    $query->where('nome', 'like', "%{$busca}%")
                        ->orWhere('cpf', 'like', "%{$busca}%")
                        ->orWhere('unidade_negocio', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%")
                        ->orWhere('cargo', 'like', "%{$busca}%");
                });
            })
            ->when($request->filled('setor_id'), fn ($query) => $query->where('setor_id', $request->integer('setor_id')))
            ->when($request->filled('unidade_negocio'), fn ($query) => $query->where('unidade_negocio', $request->string('unidade_negocio')->toString()))
            ->when($request->string('status')->toString() === 'inativos', fn ($query) => $query->where('is_active', false))
            ->when($request->string('status')->toString() !== 'inativos', fn ($query) => $query->where('is_active', true))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('colaboradores.index', [
            'colaboradores' => $colaboradores,
            'setores' => $this->setores($empresaId),
            'unidadesNegocio' => UnidadesNegocio::options($empresaId),
            'totalAtivos' => Colaborador::where('empresa_id', $empresaId)->where('is_active', true)->count(),
            'totalInativos' => Colaborador::where('empresa_id', $empresaId)->where('is_active', false)->count(),
        ]);
    }

    public function create(Request $request): View
    {
        $empresaId = $request->user()->empresa_id;

        return view('colaboradores.create', [
            'colaborador' => new Colaborador(['is_active' => true]),
            'setores' => $this->setores($empresaId),
            'gestores' => $this->gestores($empresaId),
            'formularios' => $this->formularios($empresaId),
            'unidadesNegocio' => UnidadesNegocio::options($empresaId),
        ]);
    }

    public function store(Request $request, AvaliacaoWorkflowService $workflow): RedirectResponse
    {
        $colaborador = Colaborador::create([
            ...$this->validated($request),
            'empresa_id' => $request->user()->empresa_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $workflow->garantirAgenda($colaborador);

        return redirect()->route('colaboradores.index')->with('status', 'Colaborador cadastrado e avaliações agendadas com sucesso.');
    }

    public function edit(Request $request, Colaborador $colaborador): View
    {
        $this->authorizeEmpresa($request, $colaborador);

        return view('colaboradores.edit', [
            'colaborador' => $colaborador,
            'setores' => $this->setores($request->user()->empresa_id),
            'gestores' => $this->gestores($request->user()->empresa_id),
            'formularios' => $this->formularios($request->user()->empresa_id),
            'unidadesNegocio' => UnidadesNegocio::options($request->user()->empresa_id),
        ]);
    }

    public function update(Request $request, Colaborador $colaborador, AvaliacaoWorkflowService $workflow): RedirectResponse
    {
        $this->authorizeEmpresa($request, $colaborador);

        $colaborador->update([
            ...$this->validated($request),
            'is_active' => $request->boolean('is_active'),
        ]);

        $workflow->garantirAgenda($colaborador);

        return redirect()->route('colaboradores.index')->with('status', 'Colaborador atualizado com sucesso.');
    }

    public function destroy(Request $request, Colaborador $colaborador): RedirectResponse
    {
        $this->authorizeEmpresa($request, $colaborador);

        $colaborador->update(['is_active' => false]);

        return redirect()->route('colaboradores.index')->with('status', 'Colaborador desativado com sucesso.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'setor_id' => [
                'required',
                Rule::exists('setores', 'id')->where('empresa_id', $request->user()->empresa_id),
            ],
            'gestor_id' => [
                'required',
                Rule::exists('users', 'id')->where('empresa_id', $request->user()->empresa_id)->where('role', UserRole::Gestor->value),
            ],
            'formulario_id' => [
                'required',
                Rule::exists('formularios', 'id')->where('empresa_id', $request->user()->empresa_id)->where('is_active', true),
            ],
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => [
                'nullable',
                'string',
                'max:14',
                Rule::unique('colaboradores', 'cpf')
                    ->where('empresa_id', $request->user()->empresa_id)
                    ->ignore($request->route('colaborador')?->id),
            ],
            'unidade_negocio' => ['required', 'string', 'max:255', Rule::in(UnidadesNegocio::options($request->user()->empresa_id)->all())],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'cargo' => ['required', 'string', 'max:255'],
            'data_admissao' => ['nullable', 'date'],
        ]);
    }

    private function setores(int $empresaId)
    {
        return Setor::where('empresa_id', $empresaId)
            ->where('is_active', true)
            ->orderBy('nome')
            ->get();
    }

    private function gestores(int $empresaId)
    {
        return User::where('empresa_id', $empresaId)
            ->where('role', UserRole::Gestor)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function formularios(int $empresaId)
    {
        return Formulario::where('empresa_id', $empresaId)
            ->where('is_active', true)
            ->orderBy('tipo')
            ->get();
    }

    private function authorizeEmpresa(Request $request, Colaborador $colaborador): void
    {
        abort_unless($colaborador->empresa_id === $request->user()->empresa_id, 403);
    }
}
