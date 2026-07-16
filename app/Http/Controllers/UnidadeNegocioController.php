<?php

namespace App\Http\Controllers;

use App\Models\UnidadeNegocio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnidadeNegocioController extends Controller
{
    public function index(Request $request): View
    {
        return view('unidades-negocio.index', [
            'unidades' => UnidadeNegocio::where('empresa_id', $request->user()->empresa_id)
                ->orderByDesc('is_active')
                ->orderBy('nome')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('unidades-negocio.create', [
            'unidade' => new UnidadeNegocio(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        UnidadeNegocio::create([
            ...$this->validated($request),
            'empresa_id' => $request->user()->empresa_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('unidades-negocio.index')->with('status', 'Unidade de negócio cadastrada com sucesso.');
    }

    public function edit(Request $request, UnidadeNegocio $unidades_negocio): View
    {
        $this->authorizeEmpresa($request, $unidades_negocio);

        return view('unidades-negocio.edit', ['unidade' => $unidades_negocio]);
    }

    public function update(Request $request, UnidadeNegocio $unidades_negocio): RedirectResponse
    {
        $this->authorizeEmpresa($request, $unidades_negocio);

        $unidades_negocio->update([
            ...$this->validated($request, $unidades_negocio),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('unidades-negocio.index')->with('status', 'Unidade de negócio atualizada com sucesso.');
    }

    public function destroy(Request $request, UnidadeNegocio $unidades_negocio): RedirectResponse
    {
        $this->authorizeEmpresa($request, $unidades_negocio);
        $unidades_negocio->update(['is_active' => false]);

        return redirect()->route('unidades-negocio.index')->with('status', 'Unidade de negócio desativada com sucesso.');
    }

    private function validated(Request $request, ?UnidadeNegocio $unidade = null): array
    {
        return $request->validate([
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('unidades_negocio', 'nome')
                    ->where('empresa_id', $request->user()->empresa_id)
                    ->ignore($unidade?->id),
            ],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function authorizeEmpresa(Request $request, UnidadeNegocio $unidade): void
    {
        abort_unless($unidade->empresa_id === $request->user()->empresa_id, 403);
    }
}
