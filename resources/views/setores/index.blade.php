@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Áreas" title="Setores" description="Organize os setores usados no fluxo de colaboradores e avaliações.">
    <x-slot:actions>
    <a href="{{ route('setores.create') }}" class="btn-primary">
        <i data-lucide="building-2" class="size-4"></i>
        Novo setor
    </a>
    </x-slot:actions>
</x-page-header>

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead class="bg-white/5 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Setor</th>
                <th class="px-4 py-3">Colaboradores</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            @forelse ($setores as $setor)
                <tr class="hover:bg-white/[0.03]">
                    <td class="px-4 py-4">
                        <p class="table-title">{{ $setor->nome }}</p>
                        <p class="table-subtitle">{{ $setor->descricao ?: 'Sem descrição' }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">{{ $setor->colaboradores_count }}</td>
                    <td class="px-4 py-4">
                        <span class="status-pill {{ $setor->is_active ? 'status-success' : 'status-neutral' }}">{{ $setor->is_active ? 'Ativo' : 'Inativo' }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('setores.edit', $setor) }}" class="btn-secondary px-3 py-2">
                                <i data-lucide="pencil" class="size-4"></i>
                                Editar
                            </a>
                            @if ($setor->is_active)
                                <form method="post" action="{{ route('setores.destroy', $setor) }}">
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
                <tr><td colspan="4" class="px-4 py-10 text-center text-zinc-400">Nenhum setor encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($setores as $setor)
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $setor->nome }}</h3>
                    <p class="card-description line-clamp-2">{{ $setor->descricao ?: 'Sem descrição' }}</p>
                </div>
                <span class="status-pill shrink-0 {{ $setor->is_active ? 'status-success' : 'status-neutral' }}">{{ $setor->is_active ? 'Ativo' : 'Inativo' }}</span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="mobile-field"><span>Colaboradores</span><span>{{ $setor->colaboradores_count }}</span></div>
            </div>
            <div class="mobile-actions mt-4">
                <a href="{{ route('setores.edit', $setor) }}" class="btn-secondary">
                    <i data-lucide="pencil" class="size-4"></i>
                    Editar
                </a>
                @if ($setor->is_active)
                    <form method="post" action="{{ route('setores.destroy', $setor) }}">
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
        <div class="mobile-card p-6 text-center text-sm text-slate-400">Nenhum setor encontrado.</div>
    @endforelse
</div>

<div class="mt-5">{{ $setores->links() }}</div>
@endsection
