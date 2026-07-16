@csrf

<div class="grid gap-5">
    <label class="block">
        <span class="text-sm text-zinc-300">Nome</span>
        <input name="nome" value="{{ old('nome', $setor->nome) }}" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('nome') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Descrição</span>
        <textarea name="descricao" rows="4" class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">{{ old('descricao', $setor->descricao) }}</textarea>
        @error('descricao') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>
</div>

<label class="mt-6 flex items-center gap-3 text-sm text-zinc-300">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $setor->is_active)) class="size-4 rounded border-white/10 bg-zinc-950 text-blue-500">
    Setor ativo
</label>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('setores.index') }}" class="btn-secondary">Cancelar</a>
    <button class="btn-primary">
        <i data-lucide="save" class="size-4"></i>
        Salvar
    </button>
</div>
