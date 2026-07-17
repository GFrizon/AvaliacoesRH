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

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3">Colaborador</th>
                <th class="px-4 py-3">Gestor</th>
                <th class="px-4 py-3">Formulário</th>
                <th class="px-4 py-3">Ciclo</th>
                <th class="px-4 py-3">Prazo</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            @forelse ($avaliacoes as $avaliacao)
                @php
                    $statusClasses = match ($avaliacao->status->value) {
                        'agendada' => 'status-neutral',
                        'concluida' => 'status-success',
                        'cancelada' => 'status-danger',
                        default => $avaliacao->dias_restantes < 0 ? 'status-danger' : 'status-info',
                    };
                @endphp
                <tr>
                    <td class="px-4 py-4">
                        <p class="table-title">{{ $avaliacao->colaborador->nome }}</p>
                        <p class="table-subtitle">{{ $avaliacao->colaborador->cargo }} · {{ $avaliacao->colaborador->unidade_negocio }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->gestor->name }}</td>
                    <td class="px-4 py-4 table-text">
                        <p>{{ $avaliacao->formulario->nome }}</p>
                        <p class="table-subtitle">{{ $avaliacao->formulario->tipo->label() }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->ciclo->label() }}</td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->data_limite->format('d/m/Y') }}</td>
                    <td class="px-4 py-4"><span class="status-pill {{ $statusClasses }}">{{ $avaliacao->status->label() }}</span></td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            @if (auth()->user()->isRh() && in_array($avaliacao->status->value, ['agendada', 'pendente', 'concluida'], true))
                                <form method="post" action="{{ route('avaliacoes.reenviar-email', $avaliacao) }}">
                                    @csrf
                                    <button class="btn-secondary px-3 py-2"><i data-lucide="mail" class="size-4"></i>Reenviar</button>
                                </form>
                            @endif
                            <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn-secondary px-3 py-2">Abrir</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <p class="font-semibold text-foreground">Nenhuma avaliação encontrada</p>
                        <p class="mt-1 text-sm text-foreground-muted">Ajuste os filtros ou crie uma nova avaliação.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($avaliacoes as $avaliacao)
        @php
            $statusClasses = match ($avaliacao->status->value) {
                'agendada' => 'status-neutral',
                'concluida' => 'status-success',
                'cancelada' => 'status-danger',
                default => $avaliacao->dias_restantes < 0 ? 'status-danger' : 'status-info',
            };
        @endphp
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $avaliacao->colaborador->nome }}</h3>
                    <p class="card-description truncate">{{ $avaliacao->colaborador->cargo }}</p>
                </div>
                <span class="status-pill shrink-0 {{ $statusClasses }}">{{ $avaliacao->status->label() }}</span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="mobile-field"><span>Gestor</span><span>{{ $avaliacao->gestor->name }}</span></div>
                <div class="mobile-field"><span>Unidade</span><span>{{ $avaliacao->colaborador->unidade_negocio }}</span></div>
                <div class="mobile-field"><span>Modelo</span><span>{{ $avaliacao->formulario->tipo->label() }}</span></div>
                <div class="mobile-field"><span>Ciclo</span><span>{{ $avaliacao->ciclo->label() }}</span></div>
                <div class="mobile-field"><span>Prazo</span><span>{{ $avaliacao->data_limite->format('d/m/Y') }}</span></div>
            </div>
            <div class="mobile-actions mt-4">
                @if (auth()->user()->isRh() && in_array($avaliacao->status->value, ['agendada', 'pendente', 'concluida'], true))
                    <form method="post" action="{{ route('avaliacoes.reenviar-email', $avaliacao) }}">
                        @csrf
                        <button class="btn-secondary"><i data-lucide="mail" class="size-4"></i>Reenviar</button>
                    </form>
                @endif
                <a href="{{ route('avaliacoes.show', $avaliacao) }}" class="btn-primary">Abrir</a>
            </div>
        </article>
    @empty
        <div class="mobile-card p-6 text-center">
            <p class="font-semibold text-foreground">Nenhuma avaliação encontrada</p>
            <p class="mt-1 text-sm text-foreground-muted">Ajuste os filtros para ver outros ciclos.</p>
        </div>
    @endforelse
</div>

<div class="mt-5">{{ $avaliacoes->links() }}</div>
@endsection
