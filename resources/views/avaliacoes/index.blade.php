@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Fluxo de desempenho" title="Avaliações" description="Acompanhe ciclos, prazos e respostas das avaliações em andamento.">
    <x-slot:actions>
        @if (auth()->user()->isRh())
            <a href="{{ route('avaliacoes.create') }}" class="btn-primary">
                <i data-lucide="clipboard-plus" class="size-4"></i>
                Nova avaliação
            </a>
        @endif
    </x-slot:actions>
</x-page-header>

<form
    class="filter-card mb-6 grid gap-3 lg:grid-cols-[1.3fr_repeat(4,minmax(0,1fr))_auto]"
    x-data="{ timer: null, submitSoon() { clearTimeout(this.timer); this.timer = setTimeout(() => this.$el.requestSubmit(), 450) } }"
>
    <input name="busca" value="{{ request('busca') }}" placeholder="Buscar colaborador, CPF ou cargo" class="app-input min-h-10 px-3 text-sm" @input="submitSoon">

    <select name="status" class="app-input min-h-10 px-3 text-sm" @change="$el.form.requestSubmit()">
        <option value="">Todos os status</option>
        @foreach ($statusOptions as $status)
            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>

    @if (auth()->user()->isRh())
        <select name="gestor_id" class="app-input min-h-10 px-3 text-sm" @change="$el.form.requestSubmit()">
            <option value="">Todos os gestores</option>
            @foreach ($gestores as $gestor)
                <option value="{{ $gestor->id }}" @selected((string) request('gestor_id') === (string) $gestor->id)>{{ $gestor->name }}</option>
            @endforeach
        </select>
    @endif

    <select name="unidade_negocio" class="app-input min-h-10 px-3 text-sm" @change="$el.form.requestSubmit()">
        <option value="">Todas as unidades</option>
        @foreach ($unidadesNegocio as $unidade)
            <option value="{{ $unidade }}" @selected(request('unidade_negocio') === $unidade)>{{ $unidade }}</option>
        @endforeach
    </select>

    <select name="ciclo" class="app-input min-h-10 px-3 text-sm" @change="$el.form.requestSubmit()">
        <option value="">Todos os ciclos</option>
        @foreach ($ciclos as $ciclo)
            <option value="{{ $ciclo->value }}" @selected(request('ciclo') === $ciclo->value)>{{ $ciclo->label() }}</option>
        @endforeach
    </select>

    <div class="flex gap-2">
        <noscript><button class="btn-primary px-3">Filtrar</button></noscript>
        @if (request()->hasAny(['busca', 'status', 'gestor_id', 'unidade_negocio', 'ciclo']))
            <a href="{{ route('avaliacoes.index') }}" class="btn-secondary px-3" title="Limpar filtros">
                <i data-lucide="x" class="size-4"></i>
                Limpar
            </a>
        @endif
    </div>
</form>

<div class="mb-3 flex items-center justify-between gap-3 text-sm text-foreground-muted">
    <p>{{ $grupos->count() }} grupo{{ $grupos->count() === 1 ? '' : 's' }} · {{ $totalAvaliacoes }} ciclo{{ $totalAvaliacoes === 1 ? '' : 's' }}</p>
</div>

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3">Colaborador</th>
                <th class="px-4 py-3">Gestor</th>
                <th class="px-4 py-3">Formulário</th>
                <th class="px-4 py-3">Resumo</th>
                <th class="px-4 py-3 text-right"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border" x-data="{ open: {} }">
            @forelse ($grupos as $grupo)
                @php
                    $groupKey = 'grupo_' . $loop->index;
                    $pendentes = $grupo['avaliacoes']->filter(fn ($avaliacao) => in_array($avaliacao->status->value, ['agendada', 'pendente'], true))->count();
                    $concluidas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'concluida')->count();
                    $canceladas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'cancelada')->count();
                    $efetivadas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'concluida' && $avaliacao->efetivar === true)->count();
                    $naoEfetivadas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'concluida' && $avaliacao->efetivar === false)->count();
                    $proximoPrazo = $grupo['avaliacoes']->sortBy('data_limite')->first()?->data_limite;
                @endphp
                <tr
                    class="evaluation-group-row"
                    role="button"
                    tabindex="0"
                    @click="open['{{ $groupKey }}'] = !open['{{ $groupKey }}']"
                    @keydown.enter.prevent="open['{{ $groupKey }}'] = !open['{{ $groupKey }}']"
                    @keydown.space.prevent="open['{{ $groupKey }}'] = !open['{{ $groupKey }}']"
                    :aria-expanded="!!open['{{ $groupKey }}']"
                >
                    <td class="px-4 py-4">
                        <p class="table-title">{{ $grupo['colaborador']->nome }}</p>
                        <p class="table-subtitle">{{ $grupo['colaborador']->cargo }} · {{ $grupo['colaborador']->unidade_negocio }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">{{ $grupo['gestor']->name }}</td>
                    <td class="px-4 py-4 table-text">
                        <p>{{ $grupo['formulario']->nome }}</p>
                        <p class="table-subtitle">{{ $grupo['formulario']->tipo->label() }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="cycle-summary">
                            <span class="status-pill status-info">{{ $grupo['avaliacoes']->count() }} ciclos</span>
                            @if ($pendentes > 0)
                                <span class="status-pill status-neutral">{{ $pendentes }} abertos</span>
                            @endif
                            @if ($concluidas > 0)
                                <span class="status-pill status-success">{{ $concluidas }} concluída{{ $concluidas === 1 ? '' : 's' }}</span>
                            @endif
                            @if ($efetivadas > 0)
                                <span class="status-pill status-success">{{ $efetivadas }} efetivar</span>
                            @endif
                            @if ($naoEfetivadas > 0)
                                <span class="status-pill status-danger">{{ $naoEfetivadas }} não efetivar</span>
                            @endif
                            @if ($canceladas > 0)
                                <span class="status-pill status-danger">{{ $canceladas }} cancelada{{ $canceladas === 1 ? '' : 's' }}</span>
                            @endif
                            @if ($proximoPrazo)
                                <span class="table-subtitle">Próx: {{ $proximoPrazo->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <i data-lucide="chevron-down" class="inline size-4 text-foreground-muted transition-transform" :class="{ 'rotate-180': open['{{ $groupKey }}'] }"></i>
                    </td>
                </tr>
                <tr x-show="open['{{ $groupKey }}']" x-cloak>
                    <td colspan="5" class="bg-background-subtle px-4 py-4">
                        <div class="cycle-stack">
                            @foreach ($grupo['avaliacoes'] as $avaliacao)
                                @php
                                    $statusClasses = match ($avaliacao->status->value) {
                                        'agendada' => 'status-neutral',
                                        'concluida' => 'status-success',
                                        'cancelada' => 'status-danger',
                                        default => $avaliacao->dias_restantes < 0 ? 'status-danger' : 'status-info',
                                    };
                                @endphp
                                <div class="cycle-item">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-foreground">{{ $avaliacao->ciclo->label() }}</span>
                                            <span class="status-pill {{ $statusClasses }}">{{ $avaliacao->status->label() }}</span>
                                        </div>
                                        <p class="mt-1 text-sm text-foreground-muted">Prazo: {{ $avaliacao->data_limite->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="cycle-actions">
                                        @if (auth()->user()->isRh() && in_array($avaliacao->status->value, ['agendada', 'pendente', 'concluida'], true))
                                            <form method="post" action="{{ route('avaliacoes.reenviar-email', $avaliacao) }}">
                                                @csrf
                                                <button class="btn-secondary px-3 py-2"><i data-lucide="mail" class="size-4"></i>Reenviar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn-secondary px-3 py-2">Abrir</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                        <p class="font-semibold text-foreground">Nenhuma avaliação encontrada</p>
                        <p class="mt-1 text-sm text-foreground-muted">Ajuste os filtros ou crie uma nova avaliação.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($grupos as $grupo)
        @php
            $pendentes = $grupo['avaliacoes']->filter(fn ($avaliacao) => in_array($avaliacao->status->value, ['agendada', 'pendente'], true))->count();
            $concluidas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'concluida')->count();
            $canceladas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'cancelada')->count();
            $efetivadas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'concluida' && $avaliacao->efetivar === true)->count();
            $naoEfetivadas = $grupo['avaliacoes']->filter(fn ($avaliacao) => $avaliacao->status->value === 'concluida' && $avaliacao->efetivar === false)->count();
        @endphp
        <article class="mobile-card p-4" x-data="{ open: false }">
            <button type="button" class="w-full text-left" @click="open = !open" :aria-expanded="open">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="card-title truncate">{{ $grupo['colaborador']->nome }}</h3>
                        <p class="card-description truncate">{{ $grupo['colaborador']->cargo }}</p>
                    </div>
                    <i data-lucide="chevron-down" class="mt-1 size-4 shrink-0 text-foreground-muted transition-transform" :class="{ 'rotate-180': open }"></i>
                </div>

                <div class="cycle-summary mt-3">
                    <span class="status-pill status-info">{{ $grupo['avaliacoes']->count() }} ciclos</span>
                    @if ($pendentes > 0)
                        <span class="status-pill status-neutral">{{ $pendentes }} abertos</span>
                    @endif
                    @if ($concluidas > 0)
                        <span class="status-pill status-success">{{ $concluidas }} concluída{{ $concluidas === 1 ? '' : 's' }}</span>
                    @endif
                    @if ($efetivadas > 0)
                        <span class="status-pill status-success">{{ $efetivadas }} efetivar</span>
                    @endif
                    @if ($naoEfetivadas > 0)
                        <span class="status-pill status-danger">{{ $naoEfetivadas }} não efetivar</span>
                    @endif
                    @if ($canceladas > 0)
                        <span class="status-pill status-danger">{{ $canceladas }} cancelada{{ $canceladas === 1 ? '' : 's' }}</span>
                    @endif
                </div>
            </button>

            <div x-show="open" x-cloak>
                <div class="mt-4 space-y-3">
                    <div class="mobile-field"><span>Gestor</span><span>{{ $grupo['gestor']->name }}</span></div>
                    <div class="mobile-field"><span>Unidade</span><span>{{ $grupo['colaborador']->unidade_negocio }}</span></div>
                    <div class="mobile-field"><span>Modelo</span><span>{{ $grupo['formulario']->tipo->label() }}</span></div>
                </div>

                <div class="cycle-stack mt-4">
                    @foreach ($grupo['avaliacoes'] as $avaliacao)
                        @php
                            $statusClasses = match ($avaliacao->status->value) {
                                'agendada' => 'status-neutral',
                                'concluida' => 'status-success',
                                'cancelada' => 'status-danger',
                                default => $avaliacao->dias_restantes < 0 ? 'status-danger' : 'status-info',
                            };
                        @endphp
                        <div class="cycle-item">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-foreground">{{ $avaliacao->ciclo->label() }}</span>
                                    <span class="status-pill {{ $statusClasses }}">{{ $avaliacao->status->label() }}</span>
                                </div>
                                <p class="mt-1 text-sm text-foreground-muted">Prazo: {{ $avaliacao->data_limite->format('d/m/Y') }}</p>
                            </div>
                            <div class="cycle-actions">
                                @if (auth()->user()->isRh() && in_array($avaliacao->status->value, ['agendada', 'pendente', 'concluida'], true))
                                    <form method="post" action="{{ route('avaliacoes.reenviar-email', $avaliacao) }}">
                                        @csrf
                                        <button class="btn-secondary"><i data-lucide="mail" class="size-4"></i>Reenviar</button>
                                    </form>
                                @endif
                                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn-primary">Abrir</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    @empty
        <div class="mobile-card p-6 text-center">
            <p class="font-semibold text-foreground">Nenhuma avaliação encontrada</p>
            <p class="mt-1 text-sm text-foreground-muted">Ajuste os filtros para ver outros ciclos.</p>
        </div>
    @endforelse
</div>
@endsection
