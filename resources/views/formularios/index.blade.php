@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Modelos oficiais"
    title="Formulários"
    description="Modelos usados automaticamente nos ciclos de 90 dias, 6 meses e 1 ano."
/>

<div class="grid gap-4 lg:grid-cols-3">
    @foreach ($formularios as $formulario)
        <article class="app-card app-card-interactive p-5">
            <div class="mb-5 flex items-center justify-between gap-3">
                <span class="app-chip">{{ $formulario->tipo->label() }}</span>
                <span class="card-meta">{{ $formulario->perguntas_count }} perguntas</span>
            </div>
            <h3 class="card-title">{{ $formulario->nome }}</h3>
            <p class="card-description">{{ $formulario->descricao }}</p>
            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('formularios.show', $formulario) }}" class="btn-secondary px-3 py-2">Ver</a>
                <a href="{{ route('formularios.edit', $formulario) }}" class="btn-primary px-3 py-2">Editar</a>
            </div>
        </article>
    @endforeach
</div>
@endsection
