@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Filtros e exportação" title="Relatórios" description="Consulte avaliações por período, status, unidade e gestor.">
    <x-slot:actions>
    <a href="{{ route('relatorios.pdf', request()->query()) }}" class="btn-primary">
        <i data-lucide="file-down" class="size-4"></i>
        Exportar PDF
    </a>
    </x-slot:actions>
</x-page-header>

<form class="filter-card mb-6 grid gap-3 lg:grid-cols-6">
    <input type="date" name="inicio" value="{{ request('inicio') }}" class="app-input min-h-10 px-3 text-sm">
    <input type="date" name="fim" value="{{ request('fim') }}" class="app-input min-h-10 px-3 text-sm">
    <select name="status" class="app-input min-h-10 px-3 text-sm">
        <option value="">Todos os status</option>
        @foreach ($statusOptions as $status)
            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <select name="gestor_id" class="app-input min-h-10 px-3 text-sm">
        <option value="">Todos os gestores</option>
        @foreach ($gestores as $gestor)
            <option value="{{ $gestor->id }}" @selected((string) request('gestor_id') === (string) $gestor->id)>{{ $gestor->name }}</option>
        @endforeach
    </select>
    <select name="unidade_negocio" class="app-input min-h-10 px-3 text-sm">
        <option value="">Todas as unidades</option>
        @foreach ($unidadesNegocio as $unidade)
            <option value="{{ $unidade }}" @selected(request('unidade_negocio') === $unidade)>{{ $unidade }}</option>
        @endforeach
    </select>
    <button class="btn-secondary">
        <i data-lucide="search" class="size-4"></i>
        Filtrar
    </button>
</form>

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3">Colaborador</th>
                <th class="px-4 py-3">Unidade</th>
                <th class="px-4 py-3">Ciclo</th>
                <th class="px-4 py-3">Gestor</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Prazo</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            @forelse ($avaliacoes as $avaliacao)
                @php
                    $statusClass = match ($avaliacao->status->value) {
                        'concluida' => 'status-success',
                        'cancelada' => 'status-danger',
                        'pendente' => $avaliacao->dias_restantes < 0 ? 'status-danger' : 'status-warning',
                        default => 'status-neutral',
                    };
                @endphp
                <tr>
                    <td class="px-4 py-4">
                        <p class="table-title">{{ $avaliacao->colaborador->nome }}</p>
                        <p class="table-subtitle">{{ $avaliacao->colaborador->setor->nome }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->colaborador->unidade_negocio ?: 'Não informada' }}</td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->ciclo->label() }}</td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->gestor->name }}</td>
                    <td class="px-4 py-4"><span class="status-pill {{ $statusClass }}">{{ $avaliacao->status->label() }}</span></td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->data_limite->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center">
                        <p class="font-semibold text-foreground">Nenhum registro encontrado</p>
                        <p class="mt-1 text-sm text-foreground-muted">Ajuste os filtros para ampliar o relatório.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($avaliacoes as $avaliacao)
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $avaliacao->colaborador->nome }}</h3>
                    <p class="card-description truncate">{{ $avaliacao->colaborador->unidade_negocio ?: 'Não informada' }}</p>
                </div>
                <span class="status-pill status-info shrink-0">{{ $avaliacao->status->label() }}</span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="mobile-field"><span>Ciclo</span><span>{{ $avaliacao->ciclo->label() }}</span></div>
                <div class="mobile-field"><span>Gestor</span><span>{{ $avaliacao->gestor->name }}</span></div>
                <div class="mobile-field"><span>Prazo</span><span>{{ $avaliacao->data_limite->format('d/m/Y') }}</span></div>
            </div>
        </article>
    @empty
        <div class="mobile-card p-6 text-center text-sm text-foreground-muted">Nenhuma avaliação encontrada.</div>
    @endforelse
</div>

<div class="mt-5">{{ $avaliacoes->links() }}</div>
@endsection
