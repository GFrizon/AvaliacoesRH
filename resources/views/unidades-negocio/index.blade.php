@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Estrutura" title="Unidades de negócio" description="Mantenha uma lista controlada para classificar colaboradores e filtrar relatórios.">
    <x-slot:actions>
        <a href="{{ route('unidades-negocio.create') }}" class="btn-primary">
            <i data-lucide="building-2" class="size-4"></i>
            Nova unidade
        </a>
    </x-slot:actions>
</x-page-header>

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead class="bg-white/5 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Unidade</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            @forelse ($unidades as $unidade)
                <tr class="hover:bg-white/[0.03]">
                    <td class="px-4 py-4">
                        <p class="table-title">{{ $unidade->nome }}</p>
                        <p class="table-subtitle">{{ $unidade->descricao ?: 'Sem descrição' }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="status-pill {{ $unidade->is_active ? 'status-success' : 'status-neutral' }}">{{ $unidade->is_active ? 'Ativa' : 'Inativa' }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('unidades-negocio.edit', $unidade) }}" class="btn-secondary px-3 py-2">
                                <i data-lucide="pencil" class="size-4"></i>
                                Editar
                            </a>
                            @if ($unidade->is_active)
                                <form method="post" action="{{ route('unidades-negocio.destroy', $unidade) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="btn-danger px-3 py-2">
                                        <i data-lucide="archive" class="size-4"></i>
                                        Desativar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-10 text-center text-zinc-400">Nenhuma unidade encontrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($unidades as $unidade)
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $unidade->nome }}</h3>
                    <p class="card-description line-clamp-2">{{ $unidade->descricao ?: 'Sem descrição' }}</p>
                </div>
                <span class="status-pill shrink-0 {{ $unidade->is_active ? 'status-success' : 'status-neutral' }}">{{ $unidade->is_active ? 'Ativa' : 'Inativa' }}</span>
            </div>
            <div class="mobile-actions mt-4">
                <a href="{{ route('unidades-negocio.edit', $unidade) }}" class="btn-secondary">
                    <i data-lucide="pencil" class="size-4"></i>
                    Editar
                </a>
                @if ($unidade->is_active)
                    <form method="post" action="{{ route('unidades-negocio.destroy', $unidade) }}">
                        @csrf
                        @method('delete')
                        <button class="btn-danger">
                            <i data-lucide="archive" class="size-4"></i>
                            Desativar
                        </button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <div class="mobile-card p-6 text-center text-sm text-slate-400">Nenhuma unidade encontrada.</div>
    @endforelse
</div>

<div class="mt-5">{{ $unidades->links() }}</div>
@endsection
