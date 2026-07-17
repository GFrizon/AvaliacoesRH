@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Conta" title="Meu perfil" description="Atualize seus dados de acesso e mantenha a senha segura." />

<div class="grid gap-6 lg:grid-cols-2">
    <form method="post" action="{{ route('perfil.update') }}" class="app-card p-5">
        @csrf
        @method('put')

        <h3 class="section-title">Dados pessoais</h3>
        <p class="card-description">Essas informações aparecem no topo do sistema e nos vínculos de avaliação.</p>

        <div class="mt-5 grid gap-4">
            <label>
                <span class="form-label">Nome</span>
                <input name="name" value="{{ old('name', $usuario->name) }}" required class="app-input mt-2 w-full px-3 py-2 text-sm">
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label>
                <span class="form-label">Telefone</span>
                <input name="phone" value="{{ old('phone', $usuario->phone) }}" class="app-input mt-2 w-full px-3 py-2 text-sm">
                @error('phone') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <div class="config-row">
                <span>E-mail</span>
                <strong>{{ $usuario->email }}</strong>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button class="btn-primary">
                <i data-lucide="save" class="size-4"></i>
                Salvar perfil
            </button>
        </div>
    </form>

    <form method="post" action="{{ route('perfil.password') }}" class="app-card p-5">
        @csrf
        @method('put')

        <h3 class="section-title">Alterar senha</h3>
        <p class="card-description">Use pelo menos 8 caracteres, letras maiúsculas, minúsculas e números.</p>

        <div class="mt-5 grid gap-4">
            <label>
                <span class="form-label">Senha atual</span>
                <input type="password" name="current_password" required autocomplete="current-password" class="app-input mt-2 w-full px-3 py-2 text-sm">
                @error('current_password') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label>
                <span class="form-label">Nova senha</span>
                <input type="password" name="password" required autocomplete="new-password" class="app-input mt-2 w-full px-3 py-2 text-sm">
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label>
                <span class="form-label">Confirmar nova senha</span>
                <input type="password" name="password_confirmation" required autocomplete="new-password" class="app-input mt-2 w-full px-3 py-2 text-sm">
            </label>
        </div>

        <div class="mt-6 flex justify-end">
            <button class="btn-primary">
                <i data-lucide="save" class="size-4"></i>
                Alterar senha
            </button>
        </div>
    </form>
</div>
@endsection
