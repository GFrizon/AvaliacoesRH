@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
    <div>
        <p class="text-sm text-cyan-200">{{ $formulario->tipo->label() }}</p>
        <h2 class="mt-1 text-3xl font-semibold text-white">{{ $formulario->nome }}</h2>
        <p class="mt-2 max-w-3xl text-sm text-slate-400">{{ $formulario->descricao }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('formularios.edit', $formulario) }}" class="btn-primary">Editar</a>
        <a href="{{ route('formularios.index') }}" class="btn-secondary">Voltar</a>
    </div>
</div>

<section class="app-card rounded-xl p-5">
    <div class="space-y-4">
        @foreach ($formulario->perguntas as $pergunta)
            <article class="rounded-lg border border-white/10 bg-slate-950/45 p-4 {{ $pergunta->is_active ? '' : 'opacity-55' }}">
                <div class="flex gap-3">
                    <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-cyan-400/10 text-sm font-semibold text-cyan-100">{{ $pergunta->ordem }}</span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium text-white">{{ $pergunta->titulo }}</p>
                            @if (! $pergunta->is_active)
                                <span class="status-pill bg-slate-800 text-slate-300">Inativa</span>
                            @endif
                        </div>
                        @if ($pergunta->descricao)
                            <p class="mt-1 text-sm text-slate-400">{{ $pergunta->descricao }}</p>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection
