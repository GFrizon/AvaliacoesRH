@extends('layouts.app')

@section('content')
@php
    $totalCiclo = $cards['Avaliações agendadas'] + $cards['Avaliações pendentes'] + $cards['Avaliações concluídas'] + $cards['Avaliações canceladas'];
    $taxaConclusao = $totalCiclo > 0 ? round(($cards['Avaliações concluídas'] / $totalCiclo) * 100) : 0;
    $totalColaboradoresSetores = max(1, $setores->sum('colaboradores_count'));
    $totalUnidades = max(1, $porUnidade->sum('total'));

    $prazoHumano = function ($data) {
        $dias = now()->startOfDay()->diffInDays($data->copy()->startOfDay(), false);
        if ($dias < 0) return 'Atrasada há ' . abs($dias) . ' dia' . (abs($dias) > 1 ? 's' : '');
        if ($dias === 0) return 'Vence hoje';
        if ($dias === 1) return 'Vence amanhã';
        return 'Vence em ' . $dias . ' dias';
    };

    $statusResumo = [
        ['label' => 'Agendadas', 'value' => $cards['Avaliações agendadas'], 'class' => 'bg-surface-active'],
        ['label' => 'Pendentes', 'value' => $cards['Avaliações pendentes'], 'class' => 'bg-warning'],
        ['label' => 'Concluídas', 'value' => $cards['Avaliações concluídas'], 'class' => 'bg-success'],
        ['label' => 'Canceladas', 'value' => $cards['Avaliações canceladas'], 'class' => 'bg-danger'],
    ];
@endphp

<x-page-header eyebrow="Visão executiva" title="Dashboard RH" description="Ciclos de avaliação, pendências dos gestores e resultados de efetivação.">
    <x-slot:actions>
        <a href="{{ route('avaliacoes.index') }}" class="btn-primary">
            <i data-lucide="clipboard-check" class="size-4" aria-hidden="true"></i>
            Ver avaliações
        </a>
    </x-slot:actions>
</x-page-header>

<div class="dashboard-overview mb-6">
    <article class="overview-card overview-card-primary">
        <span class="overview-icon bg-info-background text-info">
            <i data-lucide="activity" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Saúde do ciclo</p>
            <strong>{{ $taxaConclusao }}%</strong>
            <span>{{ $cards['Avaliações concluídas'] }} de {{ $totalCiclo }} concluídas</span>
        </div>
    </article>

    <article class="overview-card {{ $cards['Avaliações pendentes'] > 0 ? 'overview-card-warning' : '' }}">
        <span class="overview-icon bg-warning-background text-warning">
            <i data-lucide="mail-warning" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Pendentes</p>
            <strong>{{ $cards['Avaliações pendentes'] }}</strong>
            <span>aguardando gestores</span>
        </div>
    </article>

    <article class="overview-card {{ $atrasadas->count() > 0 ? 'overview-card-danger' : '' }}">
        <span class="overview-icon bg-danger-background text-danger">
            <i data-lucide="alarm-clock" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Atrasadas</p>
            <strong>{{ $atrasadas->count() }}</strong>
            <span>precisam de ação</span>
        </div>
    </article>

    <article class="overview-card">
        <span class="overview-icon bg-surface-active text-info">
            <i data-lucide="calendar-clock" class="size-5" aria-hidden="true"></i>
        </span>
        <div>
            <p class="metric-label">Agendadas</p>
            <strong>{{ $cards['Avaliações agendadas'] }}</strong>
            <span>ciclos futuros</span>
        </div>
    </article>
</div>

<section class="app-card mb-6 p-5" aria-labelledby="atencao-heading">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="metric-icon bg-danger-background text-danger">
                <i data-lucide="mail-warning" class="size-4" aria-hidden="true"></i>
            </span>
            <h3 id="atencao-heading" class="section-title">Atenção agora</h3>
        </div>
        <x-badge variant="danger">{{ $atrasadas->count() }} atrasada{{ $atrasadas->count() === 1 ? '' : 's' }}</x-badge>
    </div>

    <div class="divide-y divide-border">
        @forelse ($atrasadas as $avaliacao)
            <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="flex items-center justify-between gap-4 py-3 text-sm transition-colors hover:bg-surface-hover">
                <div class="min-w-0">
                    <p class="truncate font-medium text-foreground">{{ $avaliacao->colaborador->nome }}</p>
                    <p class="truncate text-foreground-muted">{{ $avaliacao->ciclo->label() }} · {{ $avaliacao->gestor->name }} · {{ $avaliacao->colaborador->unidade_negocio }}</p>
                </div>
                <span class="shrink-0 text-sm font-medium text-danger">{{ $prazoHumano($avaliacao->data_limite) }}</span>
            </a>
        @empty
            <p class="py-6 text-sm text-foreground-muted">Nenhuma avaliação atrasada. Tudo em dia por aqui.</p>
        @endforelse
    </div>
</section>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="app-card p-5">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="section-title">Próximas a disparar</h3>
            <x-badge variant="info">{{ $proximas->count() }}</x-badge>
        </div>
        <div class="divide-y divide-border">
            @forelse ($proximas as $avaliacao)
                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="flex items-center justify-between gap-4 py-3 text-sm transition-colors hover:bg-surface-hover">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-foreground">{{ $avaliacao->colaborador->nome }}</p>
                        <p class="truncate text-foreground-muted">{{ $avaliacao->ciclo->label() }} · {{ $avaliacao->formulario->tipo->label() }}</p>
                    </div>
                    <span class="shrink-0 text-foreground-muted">{{ $prazoHumano($avaliacao->data_limite) }}</span>
                </a>
            @empty
                <p class="py-6 text-sm text-foreground-muted">Nenhum disparo previsto nos próximos 15 dias.</p>
            @endforelse
        </div>
    </section>

    <section class="app-card p-5">
        <h3 class="section-title mb-4">Últimas respostas</h3>
        <div class="divide-y divide-border">
            @forelse ($ultimas as $avaliacao)
                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="flex items-center justify-between gap-4 py-3 text-sm transition-colors hover:bg-surface-hover">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-foreground">{{ $avaliacao->colaborador->nome }}</p>
                        <p class="truncate text-foreground-muted">{{ $avaliacao->gestor->name }}</p>
                    </div>
                    <x-badge :variant="$avaliacao->efetivar ? 'success' : 'neutral'">
                        {{ $avaliacao->efetivar ? 'Efetivar' : 'Não efetivar' }}
                    </x-badge>
                </a>
            @empty
                <p class="py-6 text-sm text-foreground-muted">Nenhuma avaliação concluída ainda.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="app-card p-5">
        <h3 class="section-title mb-4">Colaboradores por unidade</h3>
        <div class="grid gap-4">
            @forelse ($porUnidade as $unidade)
                @php($percentual = round(($unidade->total / $totalUnidades) * 100))
                <div>
                    <div class="mb-1.5 flex items-center justify-between text-sm">
                        <span class="truncate font-medium text-foreground">{{ $unidade->unidade_negocio ?: 'Não informada' }}</span>
                        <span class="shrink-0 text-foreground-muted">{{ $unidade->total }} · {{ $percentual }}%</span>
                    </div>
                    <div class="metric-bar">
                        <span style="--value: {{ max(4, $percentual) }}%"></span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-foreground-muted">Nenhuma unidade com colaboradores ativos.</p>
            @endforelse
        </div>
    </section>

    <section class="app-card p-5">
        <h3 class="section-title mb-4">Colaboradores por setor</h3>
        <div class="grid gap-4">
            @forelse ($setores as $setor)
                @php($percentual = round(($setor->colaboradores_count / $totalColaboradoresSetores) * 100))
                <div>
                    <div class="mb-1.5 flex items-center justify-between text-sm">
                        <span class="truncate font-medium text-foreground">{{ $setor->nome }}</span>
                        <span class="shrink-0 text-foreground-muted">{{ $setor->colaboradores_count }} · {{ $percentual }}%</span>
                    </div>
                    <div class="metric-bar">
                        <span style="--value: {{ max(4, $percentual) }}%"></span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-foreground-muted">Nenhum setor cadastrado.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
