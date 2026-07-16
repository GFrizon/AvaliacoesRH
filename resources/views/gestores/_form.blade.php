@csrf

<div class="grid gap-5 md:grid-cols-2">
    <label class="block">
        <span class="text-sm text-zinc-300">Nome</span>
        <input name="name" value="{{ old('name', $gestor->name) }}" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('name') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">E-mail</span>
        <input type="email" name="email" value="{{ old('email', $gestor->email) }}" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('email') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Telefone</span>
        <input name="phone" value="{{ old('phone', $gestor->phone) }}" class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('phone') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Senha {{ $gestor->exists ? '(opcional)' : '' }}</span>
        <input type="password" name="password" @required(! $gestor->exists) class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('password') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>
</div>

<label class="mt-6 flex items-center gap-3 text-sm text-zinc-300">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $gestor->is_active)) class="size-4 rounded border-white/10 bg-zinc-950 text-blue-500">
    Gestor ativo
</label>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('gestores.index') }}" class="btn-secondary">Cancelar</a>
    <button class="btn-primary">
        <i data-lucide="save" class="size-4"></i>
        Salvar
    </button>
</div>
