@extends('layouts.app')

@section('content')
<x-page-header
    :eyebrow="$formulario->tipo->label()"
    :title="$formulario->nome"
    :description="$formulario->descricao"
>
    <x-slot:actions>
        <a href="{{ route('formularios.edit', $formulario) }}" class="btn-primary">Editar</a>
        <a href="{{ route('formularios.index') }}" class="btn-secondary">Voltar</a>
    </x-slot:actions>
</x-page-header>

<section class="app-card p-5">
    <div class="space-y-4">
        @foreach ($formulario->perguntas as $pergunta)
            <article class="question-item {{ $pergunta->is_active ? '' : 'opacity-60' }}">
                <div class="flex gap-3">
                    <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-cyan-400/10 text-sm font-semibold text-cyan-100">{{ $pergunta->ordem }}</span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-foreground">{{ $pergunta->titulo }}</p>
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
