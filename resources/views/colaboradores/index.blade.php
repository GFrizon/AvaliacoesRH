@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Pessoas" title="Colaboradores" description="Cadastre colaboradores, vincule setores, gestores e modelos de avaliação.">
    <x-slot:actions>
    <a href="{{ route('colaboradores.create') }}" class="btn-primary">
        <i data-lucide="user-plus" class="size-4"></i>
        Novo colaborador
    </a>
    </x-slot:actions>
</x-page-header>

<div class="mb-6 grid gap-4 sm:grid-cols-2">
    <article class="app-card rounded-2xl p-5">
        <p class="metric-label">Ativos</p>
        <p class="metric-value">{{ $totalAtivos }}</p>
    </article>
    <article class="app-card rounded-2xl p-5">
        <p class="metric-label">Inativos</p>
        <p class="metric-value">{{ $totalInativos }}</p>
    </article>
</div>

<form method="get" class="filter-card mb-5 grid gap-3 lg:grid-cols-[1fr_220px_220px_180px_auto]">
    <label>
        <span class="sr-only">Buscar</span>
        <input name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome, CPF, unidade, e-mail ou cargo" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-blue-400">
    </label>

    <label>
        <span class="sr-only">Setor</span>
        <select name="setor_id" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
            <option value="">Todos os setores</option>
            @foreach ($setores as $setor)
                <option value="{{ $setor->id }}" @selected((int) request('setor_id') === $setor->id)>{{ $setor->nome }}</option>
            @endforeach
        </select>
    </label>

    <label>
        <span class="sr-only">Unidade de negócio</span>
        <select name="unidade_negocio" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
            <option value="">Todas as unidades</option>
            @foreach ($unidadesNegocio as $unidade)
                <option value="{{ $unidade }}" @selected(request('unidade_negocio') === $unidade)>{{ $unidade }}</option>
            @endforeach
        </select>
    </label>

    <label>
        <span class="sr-only">Status</span>
        <select name="status" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
            <option value="ativos" @selected(request('status', 'ativos') === 'ativos')>Ativos</option>
            <option value="inativos" @selected(request('status') === 'inativos')>Inativos</option>
        </select>
    </label>

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
                <th class="px-4 py-3">Unidade</th>
                <th class="px-4 py-3">Fluxo</th>
                <th class="px-4 py-3">Contato</th>
                <th class="px-4 py-3">Admissão</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            @forelse ($colaboradores as $colaborador)
                <tr>
                    <td class="px-4 py-4">
                        <p class="table-title">{{ $colaborador->nome }}</p>
                        <p class="table-subtitle">{{ $colaborador->cargo }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">{{ $colaborador->setor->nome }}</td>
                    <td class="px-4 py-4 table-text">{{ $colaborador->unidade_negocio ?: '-' }}</td>
                    <td class="px-4 py-4 table-text">
                        <p>{{ $colaborador->gestor?->name ?: 'Sem gestor' }}</p>
                        <p class="table-subtitle">{{ $colaborador->formulario?->tipo->label() ?: 'Sem modelo' }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">
                        <p>{{ $colaborador->email ?: 'Sem e-mail' }}</p>
                        <p class="table-subtitle">{{ $colaborador->telefone ?: 'Sem telefone' }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">{{ $colaborador->data_admissao?->format('d/m/Y') ?: '-' }}</td>
                    <td class="px-4 py-4">
                        <span class="status-pill {{ $colaborador->is_active ? 'status-success' : 'status-neutral' }}">
                            {{ $colaborador->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('colaboradores.edit', $colaborador) }}" class="btn-secondary px-3 py-2">
                                <i data-lucide="pencil" class="size-4"></i>
                                Editar
                            </a>
                            @if ($colaborador->is_active)
                                <form method="post" action="{{ route('colaboradores.destroy', $colaborador) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="btn-danger px-3 py-2">
                                        <i data-lucide="user-x" class="size-4"></i>
                                        Desativar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-zinc-400">Nenhum colaborador encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($colaboradores as $colaborador)
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $colaborador->nome }}</h3>
                    <p class="card-description truncate">{{ $colaborador->cargo }}</p>
                </div>
                <span class="status-pill shrink-0 {{ $colaborador->is_active ? 'status-success' : 'status-neutral' }}">
                    {{ $colaborador->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="mobile-field"><span>Setor</span><span>{{ $colaborador->setor->nome }}</span></div>
                <div class="mobile-field"><span>Unidade</span><span>{{ $colaborador->unidade_negocio ?: '-' }}</span></div>
                <div class="mobile-field"><span>Gestor</span><span>{{ $colaborador->gestor?->name ?: 'Sem gestor' }}</span></div>
                <div class="mobile-field"><span>Modelo</span><span>{{ $colaborador->formulario?->tipo->label() ?: 'Sem modelo' }}</span></div>
                <div class="mobile-field"><span>Contato</span><span>{{ $colaborador->email ?: $colaborador->telefone ?: 'Sem contato' }}</span></div>
                <div class="mobile-field"><span>Admissão</span><span>{{ $colaborador->data_admissao?->format('d/m/Y') ?: '-' }}</span></div>
            </div>
            <div class="mobile-actions mt-4">
                <a href="{{ route('colaboradores.edit', $colaborador) }}" class="btn-secondary">
                    <i data-lucide="pencil" class="size-4"></i>
                    Editar
                </a>
                @if ($colaborador->is_active)
                    <form method="post" action="{{ route('colaboradores.destroy', $colaborador) }}">
                        @csrf
                        @method('delete')
                        <button class="btn-danger">
                            <i data-lucide="user-x" class="size-4"></i>
                            Desativar
                        </button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <div class="mobile-card p-6 text-center text-sm text-slate-400">Nenhum colaborador encontrado.</div>
    @endforelse
</div>

<div class="mt-5">{{ $colaboradores->links() }}</div>
@endsection
