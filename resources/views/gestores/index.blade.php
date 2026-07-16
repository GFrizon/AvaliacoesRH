@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Responsáveis" title="Gestores" description="Usuários responsáveis por responder avaliações dos colaboradores.">
    <x-slot:actions>
    <a href="{{ route('gestores.create') }}" class="btn-primary">
        <i data-lucide="user-plus" class="size-4"></i>
        Novo gestor
    </a>
    </x-slot:actions>
</x-page-header>

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead class="bg-white/5 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Gestor</th>
                <th class="px-4 py-3">Contato</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            @forelse ($gestores as $gestor)
                <tr class="hover:bg-white/[0.03]">
                    <td class="px-4 py-4 table-title">{{ $gestor->name }}</td>
                    <td class="px-4 py-4 table-text">
                        <p>{{ $gestor->email }}</p>
                        <p class="table-subtitle">{{ $gestor->phone ?: 'Sem telefone' }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="status-pill {{ $gestor->is_active ? 'status-success' : 'status-neutral' }}">{{ $gestor->is_active ? 'Ativo' : 'Inativo' }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('gestores.edit', $gestor) }}" class="btn-secondary px-3 py-2">
                                <i data-lucide="pencil" class="size-4"></i>
                                Editar
                            </a>
                            @if ($gestor->is_active)
                                <form method="post" action="{{ route('gestores.destroy', $gestor) }}">
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
                <tr><td colspan="4" class="px-4 py-10 text-center text-zinc-400">Nenhum gestor encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($gestores as $gestor)
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $gestor->name }}</h3>
                    <p class="card-description truncate">{{ $gestor->email }}</p>
                </div>
                <span class="status-pill shrink-0 {{ $gestor->is_active ? 'status-success' : 'status-neutral' }}">{{ $gestor->is_active ? 'Ativo' : 'Inativo' }}</span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="mobile-field"><span>Telefone</span><span>{{ $gestor->phone ?: 'Sem telefone' }}</span></div>
            </div>
            <div class="mobile-actions mt-4">
                <a href="{{ route('gestores.edit', $gestor) }}" class="btn-secondary">
                    <i data-lucide="pencil" class="size-4"></i>
                    Editar
                </a>
                @if ($gestor->is_active)
                    <form method="post" action="{{ route('gestores.destroy', $gestor) }}">
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
        <div class="mobile-card p-6 text-center text-sm text-slate-400">Nenhum gestor encontrado.</div>
    @endforelse
</div>

<div class="mt-5">{{ $gestores->links() }}</div>
@endsection
