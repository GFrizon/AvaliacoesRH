<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetorController extends Controller
{
    public function index(Request $request): View
    {
        return view('setores.index', [
            'setores' => Setor::withCount('colaboradores')
                ->where('empresa_id', $request->user()->empresa_id)
                ->orderByDesc('is_active')
                ->orderBy('nome')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('setores.create', ['setor' => new Setor(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Setor::create([
            ...$this->validated($request),
            'empresa_id' => $request->user()->empresa_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('setores.index')->with('status', 'Setor cadastrado com sucesso.');
    }

    public function edit(Request $request, Setor $setor): View
    {
        $this->authorizeEmpresa($request, $setor);

        return view('setores.edit', compact('setor'));
    }

    public function update(Request $request, Setor $setor): RedirectResponse
    {
        $this->authorizeEmpresa($request, $setor);

        $setor->update([
            ...$this->validated($request),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('setores.index')->with('status', 'Setor atualizado com sucesso.');
    }

    public function destroy(Request $request, Setor $setor): RedirectResponse
    {
        $this->authorizeEmpresa($request, $setor);
        $setor->update(['is_active' => false]);

        return redirect()->route('setores.index')->with('status', 'Setor desativado com sucesso.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function authorizeEmpresa(Request $request, Setor $setor): void
    {
        abort_unless($setor->empresa_id === $request->user()->empresa_id, 403);
    }
}
