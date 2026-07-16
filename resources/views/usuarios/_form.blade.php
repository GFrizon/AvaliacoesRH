@csrf

<div class="grid gap-5 md:grid-cols-2">
    <label class="block">
        <span class="form-label">Nome</span>
        <input name="name" value="{{ old('name', $usuario->name) }}" required class="app-input mt-2 w-full px-3 py-2 text-sm">
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="form-label">E-mail para acesso e alertas</span>
        <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required class="app-input mt-2 w-full px-3 py-2 text-sm">
        @error('email') <span class="form-error">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="form-label">Perfil</span>
        <select name="role" required class="app-input mt-2 w-full px-3 py-2 text-sm">
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $usuario->role?->value) === $role->value)>
                    {{ $role === \App\Enums\UserRole::Rh ? 'RH - recebe alertas de conclusão' : 'Gestor - recebe avaliações pendentes' }}
                </option>
            @endforeach
        </select>
        @error('role') <span class="form-error">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="form-label">Telefone</span>
        <input name="phone" value="{{ old('phone', $usuario->phone) }}" class="app-input mt-2 w-full px-3 py-2 text-sm">
        @error('phone') <span class="form-error">{{ $message }}</span> @enderror
    </label>

    <label class="block md:col-span-2">
        <span class="form-label">Senha {{ $usuario->exists ? '(opcional)' : '' }}</span>
        <input type="password" name="password" @required(! $usuario->exists) class="app-input mt-2 w-full px-3 py-2 text-sm">
        @error('password') <span class="form-error">{{ $message }}</span> @enderror
    </label>
</div>

<label class="mt-6 flex items-center gap-3 text-sm text-foreground-muted">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $usuario->is_active)) class="size-4 rounded border-border text-primary">
    Usuário ativo
</label>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('usuarios.index') }}" class="btn-secondary">Cancelar</a>
    <button class="btn-primary">
        <i data-lucide="save" class="size-4"></i>
        Salvar
    </button>
</div>
