@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Modelo oficial"
    title="Editar formulário"
    description="Altere textos, ordem e tipos das perguntas. Perguntas removidas deixam de aparecer nas próximas avaliações, mas continuam preservadas no histórico."
>
    <x-slot:actions>
        <a href="{{ route('formularios.show', $formulario) }}" class="btn-secondary">Visualizar</a>
    </x-slot:actions>
</x-page-header>

<form method="post" action="{{ route('formularios.update', $formulario) }}" class="space-y-6">
    @csrf
    @method('put')

    <section class="app-card p-5">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="block">
                <span class="text-sm text-slate-300">Nome</span>
                <input name="nome" value="{{ old('nome', $formulario->nome) }}" required class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
                @error('nome') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm text-slate-300">Tipo</span>
                <select name="tipo" required class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
                    @foreach ($tiposFormulario as $tipo)
                        <option value="{{ $tipo->value }}" @selected(old('tipo', $formulario->tipo->value) === $tipo->value)>{{ $tipo->label() }}</option>
                    @endforeach
                </select>
                @error('tipo') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
            </label>

            <label class="block md:col-span-2">
                <span class="text-sm text-slate-300">Descrição</span>
                <textarea name="descricao" rows="3" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">{{ old('descricao', $formulario->descricao) }}</textarea>
                @error('descricao') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="mt-5 flex items-center gap-3 text-sm text-slate-300">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $formulario->is_active)) class="size-4 rounded border-white/10 bg-slate-950 text-cyan-400">
            Formulário ativo para novas avaliações
        </label>
    </section>

    <section class="app-card p-5">
        <div class="mb-5 flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
            <div>
                <h3 class="card-title">Perguntas</h3>
                <p class="text-sm text-slate-400">{{ $formulario->perguntas->where('is_active', true)->count() }} ativas de {{ $formulario->perguntas->count() }} cadastradas.</p>
            </div>
            <button class="btn-primary" type="submit">
                <i data-lucide="save" class="size-4"></i>
                Salvar alterações
            </button>
        </div>

        <div class="space-y-4">
            @foreach ($formulario->perguntas as $pergunta)
                <article class="question-item {{ $pergunta->is_active ? '' : 'opacity-60' }}">
                    <div class="grid gap-4 xl:grid-cols-[84px_1.1fr_.7fr_150px_120px_auto] xl:items-start">
                        <label>
                            <span class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500">Ordem</span>
                            <input type="number" min="1" name="perguntas[{{ $pergunta->id }}][ordem]" value="{{ old("perguntas.{$pergunta->id}.ordem", $pergunta->ordem) }}" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
                        </label>

                        <label>
                            <span class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500">Título</span>
                            <input name="perguntas[{{ $pergunta->id }}][titulo]" value="{{ old("perguntas.{$pergunta->id}.titulo", $pergunta->titulo) }}" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
                        </label>

                        <label>
                            <span class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500">Descrição</span>
                            <textarea name="perguntas[{{ $pergunta->id }}][descricao]" rows="2" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">{{ old("perguntas.{$pergunta->id}.descricao", $pergunta->descricao) }}</textarea>
                        </label>

                        <label>
                            <span class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500">Tipo</span>
                            <select name="perguntas[{{ $pergunta->id }}][tipo]" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
                                @foreach ($tiposPergunta as $tipo)
                                    <option value="{{ $tipo->value }}" @selected(old("perguntas.{$pergunta->id}.tipo", $pergunta->tipo->value) === $tipo->value)>{{ $tipo->label() }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="flex items-center gap-3 pt-7 text-sm text-slate-300 xl:justify-center">
                            <input type="checkbox" name="perguntas[{{ $pergunta->id }}][obrigatoria]" value="1" @checked(old("perguntas.{$pergunta->id}.obrigatoria", $pergunta->obrigatoria)) class="size-4 rounded border-white/10 bg-slate-950 text-cyan-400">
                            Obrigatória
                        </label>

                        <div class="flex gap-2 pt-6 xl:justify-end">
                            @if ($pergunta->is_active)
                                <button class="btn-danger px-3 py-2" form="delete-question-{{ $pergunta->id }}" type="submit">
                                    Remover
                                </button>
                            @else
                                <button class="btn-secondary px-3 py-2" form="restore-question-{{ $pergunta->id }}" type="submit">
                                    Reativar
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</form>

@foreach ($formulario->perguntas as $pergunta)
    <form id="delete-question-{{ $pergunta->id }}" method="post" action="{{ route('formularios.perguntas.destroy', [$formulario, $pergunta]) }}">
        @csrf
        @method('delete')
    </form>
    <form id="restore-question-{{ $pergunta->id }}" method="post" action="{{ route('formularios.perguntas.restore', [$formulario, $pergunta]) }}">
        @csrf
        @method('patch')
    </form>
@endforeach

<section class="app-card mt-6 p-5">
    <h3 class="card-title">Adicionar pergunta</h3>
    <form method="post" action="{{ route('formularios.perguntas.store', $formulario) }}" class="mt-4 grid gap-4 lg:grid-cols-[1fr_1fr_180px_auto] lg:items-end">
        @csrf
        <label>
            <span class="text-sm text-slate-300">Título</span>
            <input name="titulo" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
            @error('titulo') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
        </label>
        <label>
            <span class="text-sm text-slate-300">Descrição</span>
            <input name="descricao" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
            @error('descricao') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
        </label>
        <label>
            <span class="text-sm text-slate-300">Tipo</span>
            <select name="tipo" class="mt-2 w-full rounded-lg px-3 py-2 text-sm outline-none transition">
                @foreach ($tiposPergunta as $tipo)
                    <option value="{{ $tipo->value }}" @selected($tipo === \App\Enums\PerguntaTipo::TextoLongo)>{{ $tipo->label() }}</option>
                @endforeach
            </select>
            @error('tipo') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
        </label>
        <button class="btn-primary">
            <i data-lucide="plus" class="size-4"></i>
            Adicionar
        </button>
    </form>
</section>
@endsection
