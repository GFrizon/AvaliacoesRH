@extends('layouts.app')

@section('content')
@php
    // Apenas apresentação: nenhuma regra de negócio é recalculada aqui,
    // somente combinações e formatação dos dados já enviados pelo controller.
    $totalCiclo = $cards['Avaliações agendadas'] + $cards['Avaliações pendentes'] + $cards['Avaliações concluídas'] + $cards['Avaliações canceladas'];
    $taxaConclusao = $totalCiclo > 0 ? round(($cards['Avaliações concluídas'] / $totalCiclo) * 100) : 0;
    $totalColaboradoresSetores = max(1, $setores->sum('colaboradores_count'));

    $prazoHumano = function ($data) {
        $dias = now()->startOfDay()->diffInDays($data->copy()->startOfDay(), false);
        if ($dias < 0) return 'Atrasada há ' . abs($dias) . ' dia' . (abs($dias) > 1 ? 's' : '');
        if ($dias === 0) return 'Vence hoje';
        if ($dias === 1) return 'Vence amanhã';
        return 'Vence em ' . $dias . ' dias';
    };
@endphp

<x-page-header eyebrow="Visão executiva" title="Dashboard RH" description="Ciclos de avaliação, pendências dos gestores e resultados de efetivação.">
    <x-slot:actions>
        <a href="{{ route('avaliacoes.index') }}" class="btn-primary">
            <i data-lucide="clipboard-check" class="size-4" aria-hidden="true"></i>
            Ver avaliações
        </a>
    </x-slot:actions>
</x-page-header>

<section class="app-card mb-6 p-5" aria-labelledby="atencao-heading">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="metric-icon bg-danger-background text-danger">
                <i data-lucide="mail-warning" class="size-4" aria-hidden="true"></i>
            </span>
            <h3 id="atencao-heading" class="text-base font-semibold text-foreground">Requer atenção - avaliações atrasadas</h3>
        </div>
        <x-badge variant="danger">{{ $atrasadas->count() }} atrasada{{ $atrasadas->count() === 1 ? '' : 's' }}</x-badge>
    </div>

    <div class="divide-y divide-border">
        @forelse ($atrasadas as $avaliacao)
            <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="flex items-center justify-between gap-4 py-3 text-sm transition-colors hover:bg-surface-hover">
                <div class="min-w-0">
                    <p class="truncate font-medium text-foreground">{{ $avaliacao->colaborador->nome }}</p>
                    <p class="truncate text-foreground-muted">{{ $avaliacao->ciclo->label() }} · {{ $avaliacao->gestor->name }}</p>
                </div>
                <span class="shrink-0 text-sm font-medium text-danger">{{ $prazoHumano($avaliacao->data_limite) }}</span>
            </a>
        @empty
            <p class="py-6 text-sm text-foreground-muted">Nenhuma avaliação atrasada. Tudo em dia por aqui.</p>
        @endforelse
    </div>
</section>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <x-stat-card
        label="Avaliações pendentes"
        :value="$cards['Avaliações pendentes']"
        icon="mail-warning"
        tone="warning"
        hint="aguardando resposta do gestor"
    />

    <x-stat-card
        label="Avaliações concluídas"
        :value="$cards['Avaliações concluídas']"
        icon="badge-check"
        tone="success"
        :hint="$cards['Avaliações concluídas'] . ' de ' . $totalCiclo . ' avaliações · ' . $taxaConclusao . '% concluídas'"
    />

    <x-stat-card
        label="Avaliações agendadas"
        :value="$cards['Avaliações agendadas']"
        icon="calendar-clock"
        tone="default"
        hint="ciclos programados"
    />
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="app-card p-5">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-foreground">Próximas a disparar</h3>
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
        <h3 class="mb-4 text-sm font-semibold text-foreground">Últimas avaliações respondidas</h3>
        <div class="divide-y divide-border">
            @forelse ($ultimas as $avaliacao)
                <div class="flex items-center justify-between gap-4 py-3 text-sm">
                    <div class="min-w-0">
                        <p class="truncate font-medium text-foreground">{{ $avaliacao->colaborador->nome }}</p>
                        <p class="truncate text-foreground-muted">{{ $avaliacao->gestor->name }}</p>
                    </div>
                    <x-badge :variant="$avaliacao->efetivar ? 'success' : 'neutral'">
                        {{ $avaliacao->efetivar ? 'Efetivar' : 'Não efetivar' }}
                    </x-badge>
                </div>
            @empty
                <p class="py-6 text-sm text-foreground-muted">Nenhuma avaliação concluída ainda.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-stat-card label="Gestores" :value="$cards['Gestores']" icon="user-check" hint="responsáveis ativos" />
    <x-stat-card label="Colaboradores" :value="$cards['Colaboradores']" icon="users" hint="em acompanhamento" />
    <x-stat-card label="Efetivados" :value="$cards['Efetivados']" icon="thumbs-up" tone="success" hint="resultado positivo" />
    <x-stat-card label="Não efetivados" :value="$cards['Não efetivados']" icon="thumbs-down" tone="danger" hint="resultado negativo" />
</div>

<section class="app-card mt-6 p-5">
    <h3 class="mb-4 text-sm font-semibold text-foreground">Colaboradores por setor</h3>
    <div class="grid gap-4 sm:grid-cols-2">
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
@endsection
