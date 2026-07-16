<?php

namespace App\Http\Controllers;

use App\Enums\FormularioTipo;
use App\Enums\PerguntaTipo;
use App\Models\Formulario;
use App\Models\Pergunta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FormularioController extends Controller
{
    public function index(Request $request): View
    {
        return view('formularios.index', [
            'formularios' => Formulario::withCount('perguntas')
                ->where('empresa_id', $request->user()->empresa_id)
                ->orderBy('tipo')
                ->get(),
        ]);
    }

    public function show(Request $request, Formulario $formulario): View
    {
        abort_unless($formulario->empresa_id === $request->user()->empresa_id, 403);

        return view('formularios.show', [
            'formulario' => $formulario->load('perguntas'),
        ]);
    }

    public function edit(Request $request, Formulario $formulario): View
    {
        $this->authorizeEmpresa($request, $formulario);

        return view('formularios.edit', [
            'formulario' => $formulario->load('perguntas.respostas'),
            'tiposFormulario' => FormularioTipo::cases(),
            'tiposPergunta' => PerguntaTipo::cases(),
        ]);
    }

    public function update(Request $request, Formulario $formulario): RedirectResponse
    {
        $this->authorizeEmpresa($request, $formulario);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'tipo' => ['required', Rule::enum(FormularioTipo::class)],
            'is_active' => ['nullable', 'boolean'],
            'perguntas' => ['array'],
            'perguntas.*.titulo' => ['required', 'string', 'max:255'],
            'perguntas.*.descricao' => ['nullable', 'string', 'max:5000'],
            'perguntas.*.tipo' => ['required', Rule::enum(PerguntaTipo::class)],
            'perguntas.*.ordem' => ['required', 'integer', 'min:1'],
            'perguntas.*.obrigatoria' => ['nullable', 'boolean'],
        ]);

        $formulario->update([
            'nome' => $validated['nome'],
            'descricao' => $validated['descricao'] ?? null,
            'tipo' => $validated['tipo'],
            'is_active' => $request->boolean('is_active'),
        ]);

        foreach ($validated['perguntas'] ?? [] as $id => $perguntaData) {
            $pergunta = $formulario->perguntas()->whereKey($id)->firstOrFail();

            $pergunta->update([
                'titulo' => $perguntaData['titulo'],
                'descricao' => $perguntaData['descricao'] ?? null,
                'tipo' => $perguntaData['tipo'],
                'ordem' => $perguntaData['ordem'],
                'obrigatoria' => (bool) ($perguntaData['obrigatoria'] ?? false),
            ]);
        }

        return redirect()->route('formularios.edit', $formulario)->with('status', 'Formulário atualizado com sucesso.');
    }

    public function storePergunta(Request $request, Formulario $formulario): RedirectResponse
    {
        $this->authorizeEmpresa($request, $formulario);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'tipo' => ['required', Rule::enum(PerguntaTipo::class)],
            'obrigatoria' => ['nullable', 'boolean'],
        ]);

        $formulario->perguntas()->create([
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'] ?? null,
            'tipo' => $validated['tipo'],
            'ordem' => ((int) $formulario->perguntas()->max('ordem')) + 1,
            'obrigatoria' => $request->boolean('obrigatoria', true),
            'is_active' => true,
        ]);

        return redirect()->route('formularios.edit', $formulario)->with('status', 'Pergunta adicionada com sucesso.');
    }

    public function destroyPergunta(Request $request, Formulario $formulario, Pergunta $pergunta): RedirectResponse
    {
        $this->authorizeEmpresa($request, $formulario);
        abort_unless($pergunta->formulario_id === $formulario->id, 404);

        $pergunta->update(['is_active' => false]);

        return redirect()->route('formularios.edit', $formulario)->with('status', 'Pergunta removida das próximas avaliações.');
    }

    public function restorePergunta(Request $request, Formulario $formulario, Pergunta $pergunta): RedirectResponse
    {
        $this->authorizeEmpresa($request, $formulario);
        abort_unless($pergunta->formulario_id === $formulario->id, 404);

        $pergunta->update(['is_active' => true]);

        return redirect()->route('formularios.edit', $formulario)->with('status', 'Pergunta reativada com sucesso.');
    }

    private function authorizeEmpresa(Request $request, Formulario $formulario): void
    {
        abort_unless($formulario->empresa_id === $request->user()->empresa_id, 403);
    }
}
