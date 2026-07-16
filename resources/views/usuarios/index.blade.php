@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Acessos e alertas" title="Usuários" description="Cadastre quem acessa o sistema e defina quais e-mails recebem alertas operacionais.">
    <x-slot:actions>
        <a href="{{ route('usuarios.create') }}" class="btn-primary">
            <i data-lucide="user-plus" class="size-4"></i>
            Novo usuário
        </a>
    </x-slot:actions>
</x-page-header>

<div class="mb-5 grid gap-4 lg:grid-cols-2">
    <section class="app-card p-5">
        <h3 class="card-title">Quem recebe o que?</h3>
        <p class="card-description">Gestores recebem e-mails de avaliações pendentes dos colaboradores vinculados a eles.</p>
    </section>
    <section class="app-card p-5">
        <h3 class="card-title">Alertas para RH</h3>
        <p class="card-description">Usuários com perfil RH recebem avisos quando uma avaliação é concluída.</p>
    </section>
</div>

<div class="desktop-table table-shell">
    <table class="w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3">Usuário</th>
                <th class="px-4 py-3">Perfil</th>
                <th class="px-4 py-3">Contato</th>
                <th class="px-4 py-3">Alertas</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            @forelse ($usuarios as $usuario)
                <tr>
                    <td class="px-4 py-4 table-title">{{ $usuario->name }}</td>
                    <td class="px-4 py-4">
                        <span class="app-chip">{{ $usuario->role === \App\Enums\UserRole::Rh ? 'RH' : 'Gestor' }}</span>
                    </td>
                    <td class="px-4 py-4 table-text">
                        <p>{{ $usuario->email }}</p>
                        <p class="table-subtitle">{{ $usuario->phone ?: 'Sem telefone' }}</p>
                    </td>
                    <td class="px-4 py-4 table-text">
                        {{ $usuario->role === \App\Enums\UserRole::Rh ? 'Conclusões de avaliações' : 'Avaliações pendentes' }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="status-pill {{ $usuario->is_active ? 'status-success' : 'status-neutral' }}">
                            {{ $usuario->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-secondary px-3 py-2">
                                <i data-lucide="pencil" class="size-4"></i>
                                Editar
                            </a>
                            @if ($usuario->is_active && ! $usuario->is(auth()->user()))
                                <form method="post" action="{{ route('usuarios.destroy', $usuario) }}">
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
                    <td colspan="6" class="px-4 py-10 text-center text-foreground-muted">Nenhum usuário encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mobile-list gap-3">
    @forelse ($usuarios as $usuario)
        <article class="mobile-card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="card-title truncate">{{ $usuario->name }}</h3>
                    <p class="card-description truncate">{{ $usuario->email }}</p>
                </div>
                <span class="status-pill shrink-0 {{ $usuario->is_active ? 'status-success' : 'status-neutral' }}">
                    {{ $usuario->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
            <div class="mt-4 space-y-3">
                <div class="mobile-field"><span>Perfil</span><span>{{ $usuario->role === \App\Enums\UserRole::Rh ? 'RH' : 'Gestor' }}</span></div>
                <div class="mobile-field"><span>Telefone</span><span>{{ $usuario->phone ?: 'Sem telefone' }}</span></div>
                <div class="mobile-field"><span>Alertas</span><span>{{ $usuario->role === \App\Enums\UserRole::Rh ? 'Conclusões' : 'Pendências' }}</span></div>
            </div>
            <div class="mobile-actions mt-4">
                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-secondary">
                    <i data-lucide="pencil" class="size-4"></i>
                    Editar
                </a>
            </div>
        </article>
    @empty
        <div class="mobile-card p-6 text-center text-sm text-foreground-muted">Nenhum usuário encontrado.</div>
    @endforelse
</div>

<div class="mt-5">{{ $usuarios->links() }}</div>
@endsection
