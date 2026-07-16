@extends('layouts.app')

@section('content')
@php
    $primeiroNome = strtok(auth()->user()->name, ' ');
    $prazoLabel = function ($dias) {
        if ($dias < 0) return 'Atrasada ha ' . abs($dias) . ' dia' . (abs($dias) === 1 ? '' : 's');
        if ($dias === 0) return 'Vence hoje';
        if ($dias === 1) return 'Vence amanha';
        return 'Vence em ' . $dias . ' dias';
    };
@endphp

<x-page-header
    eyebrow="Bom dia, {{ $primeiroNome }}"
    title="Suas avaliações"
    description="Responda os ciclos liberados pelo RH dentro do prazo."
/>

<div class="grid gap-4 md:grid-cols-3">
    <article class="dashboard-signal {{ $atrasadasCount > 0 ? 'dashboard-signal-danger' : '' }}">
        <p class="metric-label">Atrasadas</p>
        <p class="metric-value">{{ $atrasadasCount }}</p>
    </article>
    <article class="dashboard-signal {{ $venceHojeCount > 0 ? 'dashboard-signal-warning' : '' }}">
        <p class="metric-label">Vencem hoje</p>
        <p class="metric-value">{{ $venceHojeCount }}</p>
    </article>
    <article class="dashboard-signal">
        <p class="metric-label">Concluidas</p>
        <p class="metric-value">{{ $concluidasCount }}</p>
    </article>
</div>

<div class="mt-6 flex items-center justify-between gap-4">
    <div>
        <h3 class="section-title">Pendentes</h3>
        <p class="card-description mt-1">Priorizadas por data limite.</p>
    </div>
    <span class="status-pill status-info">{{ $pendentes->count() }} pendente{{ $pendentes->count() === 1 ? '' : 's' }}</span>
</div>

<div class="mt-4 grid gap-4 lg:grid-cols-2">
    @forelse ($pendentes as $avaliacao)
        @php
            $dias = $avaliacao->dias_restantes;
            $cardState = $dias < 0 ? 'evaluation-task-danger' : ($dias <= 1 ? 'evaluation-task-warning' : 'evaluation-task-normal');
            $badgeState = $dias < 0 ? 'status-danger' : ($dias <= 1 ? 'status-warning' : 'status-info');
        @endphp
        <article class="evaluation-task {{ $cardState }}">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="task-kicker">{{ $avaliacao->ciclo->label() }}</p>
                    <h3 class="card-title truncate">{{ $avaliacao->colaborador->nome }}</h3>
                    <p class="card-description truncate">{{ $avaliacao->colaborador->cargo }} - {{ $avaliacao->colaborador->setor->nome }}</p>
                </div>
                <span class="status-pill shrink-0 {{ $badgeState }}">{{ $prazoLabel($dias) }}</span>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <span class="app-chip">{{ $avaliacao->formulario->tipo->label() }}</span>
                <span class="status-pill status-neutral">Prazo: {{ $avaliacao->data_limite->format('d/m/Y') }}</span>
            </div>

            <div class="mt-5 flex justify-end">
                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn-primary">Responder</a>
            </div>
        </article>
    @empty
        <section class="app-card p-6 lg:col-span-2">
            <h3 class="card-title">Tudo em dia</h3>
            <p class="card-description">Você não possui avaliações pendentes no momento.</p>
        </section>
    @endforelse
</div>
@endsection
