@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Filtros e exportação" title="Relatórios" description="Consulte avaliações por período, status e gere arquivos para acompanhamento.">
    <x-slot:actions>
    <a href="{{ route('relatorios.pdf', request()->query()) }}" class="btn-primary">
        <i data-lucide="file-down" class="size-4"></i>
        Exportar PDF
    </a>
    </x-slot:actions>
</x-page-header>

<form class="filter-card mb-6 grid gap-3 md:grid-cols-4">
    <input type="date" name="inicio" value="{{ request('inicio') }}" class="rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm">
    <input type="date" name="fim" value="{{ request('fim') }}" class="rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm">
    <select name="status" class="rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm">
        <option value="">Todos os status</option>
        <option value="agendada" @selected(request('status') === 'agendada')>Agendada</option>
        <option value="pendente" @selected(request('status') === 'pendente')>Pendente</option>
        <option value="concluida" @selected(request('status') === 'concluida')>Concluida</option>
        <option value="cancelada" @selected(request('status') === 'cancelada')>Cancelada</option>
    </select>
    <button class="btn-secondary">
        <i data-lucide="search" class="size-4"></i>
        Filtrar
    </button>
</form>

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead class="bg-white/5 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Colaborador</th>
                <th class="px-4 py-3">Setor</th>
                <th class="px-4 py-3">Ciclo</th>
                <th class="px-4 py-3">Gestor</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Criada em</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            @foreach ($avaliacoes as $avaliacao)
                <tr>
                    <td class="px-4 py-4 table-title">{{ $avaliacao->colaborador->nome }}</td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->colaborador->setor->nome }}</td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->ciclo->label() }}</td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->gestor->name }}</td>
                    <td class="px-4 py-4"><span class="status-pill status-info">{{ $avaliacao->status->label() }}</span></td>
                    <td class="px-4 py-4 table-text">{{ $avaliacao->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($avaliacoes as $avaliacao)
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $avaliacao->colaborador->nome }}</h3>
                    <p class="card-description truncate">{{ $avaliacao->colaborador->setor->nome }}</p>
                </div>
                <span class="status-pill status-info shrink-0">{{ $avaliacao->status->label() }}</span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="mobile-field"><span>Ciclo</span><span>{{ $avaliacao->ciclo->label() }}</span></div>
                <div class="mobile-field"><span>Gestor</span><span>{{ $avaliacao->gestor->name }}</span></div>
                <div class="mobile-field"><span>Criada em</span><span>{{ $avaliacao->created_at->format('d/m/Y') }}</span></div>
            </div>
        </article>
    @empty
        <div class="mobile-card p-6 text-center text-sm text-slate-400">Nenhuma avaliação encontrada.</div>
    @endforelse
</div>

<div class="mt-5">{{ $avaliacoes->links() }}</div>
@endsection
