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
    title="Suas avaliacoes"
    description="Acompanhe o que precisa de resposta agora e os ciclos que ja estao programados."
/>

<div class="dashboard-overview mb-6">
    <a href="{{ route('avaliacoes.index', ['status' => 'pendente']) }}" class="overview-card {{ $pendentes->count() > 0 ? 'overview-card-warning' : '' }}">
        <span class="overview-icon bg-warning-background text-warning">
            <i data-lucide="clipboard-pen" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Pendentes</p>
            <strong>{{ $pendentes->count() }}</strong>
            <span>liberadas para resposta</span>
        </div>
    </a>

    <a href="{{ route('avaliacoes.index', ['prazo' => 'atrasadas']) }}" class="overview-card {{ $atrasadasCount > 0 ? 'overview-card-danger' : '' }}">
        <span class="overview-icon bg-danger-background text-danger">
            <i data-lucide="mail-warning" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Atrasadas</p>
            <strong>{{ $atrasadasCount }}</strong>
            <span>precisam de atencao</span>
        </div>
    </a>

    <a href="{{ route('avaliacoes.index', ['status' => 'agendada']) }}" class="overview-card">
        <span class="overview-icon bg-surface-active text-info">
            <i data-lucide="calendar-clock" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Agendadas</p>
            <strong>{{ $agendadas->count() }}</strong>
            <span>ciclos futuros</span>
        </div>
    </a>

    <a href="{{ route('avaliacoes.index', ['status' => 'concluida']) }}" class="overview-card overview-card-success">
        <span class="overview-icon bg-success-background text-success">
            <i data-lucide="badge-check" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Concluidas</p>
            <strong>{{ $concluidasCount }}</strong>
            <span>{{ $venceHojeCount }} vencendo hoje</span>
        </div>
    </a>
</div>

<div class="mt-6 flex items-center justify-between gap-4">
    <div>
        <h3 class="section-title">Responder agora</h3>
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
            <p class="card-description">Voce nao possui avaliacoes pendentes no momento.</p>
        </section>
    @endforelse
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="app-card p-5">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h3 class="section-title">Agendadas</h3>
                <p class="card-description mt-1">Ciclos programados pelo RH.</p>
            </div>
            <span class="status-pill status-neutral">{{ $agendadas->count() }}</span>
        </div>

        <div class="divide-y divide-border">
            @forelse ($proximasAgendadas as $avaliacao)
                @php
                    $dias = $avaliacao->dias_restantes;
                    $badgeState = $dias < 0 ? 'status-danger' : ($dias <= 1 ? 'status-warning' : 'status-info');
                @endphp
                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="flex items-center justify-between gap-4 py-3 text-sm transition-colors hover:bg-surface-hover">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-foreground">{{ $avaliacao->colaborador->nome }}</p>
                        <p class="truncate text-foreground-muted">{{ $avaliacao->ciclo->label() }} - {{ $avaliacao->formulario->tipo->label() }}</p>
                    </div>
                    <span class="status-pill shrink-0 {{ $badgeState }}">{{ $prazoLabel($dias) }}</span>
                </a>
            @empty
                <p class="py-6 text-sm text-foreground-muted">Nenhuma avaliacao agendada para voce.</p>
            @endforelse
        </div>
    </section>

    <section class="app-card p-5">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h3 class="section-title">Concluidas recentes</h3>
                <p class="card-description mt-1">Ultimas respostas enviadas.</p>
            </div>
            <span class="status-pill status-success">{{ $concluidasCount }}</span>
        </div>

        <div class="divide-y divide-border">
            @forelse ($concluidasRecentes as $avaliacao)
                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="flex items-center justify-between gap-4 py-3 text-sm transition-colors hover:bg-surface-hover">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-foreground">{{ $avaliacao->colaborador->nome }}</p>
                        <p class="truncate text-foreground-muted">{{ $avaliacao->ciclo->label() }} - {{ optional($avaliacao->concluida_em)->format('d/m/Y') }}</p>
                    </div>
                    <span class="status-pill shrink-0 {{ $avaliacao->efetivar ? 'status-success' : 'status-neutral' }}">{{ $avaliacao->efetivar ? 'Efetivar' : 'Nao efetivar' }}</span>
                </a>
            @empty
                <p class="py-6 text-sm text-foreground-muted">Nenhuma avaliacao concluida ainda.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
