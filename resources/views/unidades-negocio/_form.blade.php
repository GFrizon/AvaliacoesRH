@csrf

<div class="grid gap-5">
    <label class="block">
        <span class="form-label">Nome</span>
        <input name="nome" value="{{ old('nome', $unidade->nome) }}" required class="app-input mt-2 w-full px-3 py-2 text-sm">
        @error('nome') <span class="form-error">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="form-label">Descrição</span>
        <textarea name="descricao" rows="4" class="app-input mt-2 w-full px-3 py-2 text-sm">{{ old('descricao', $unidade->descricao) }}</textarea>
        @error('descricao') <span class="form-error">{{ $message }}</span> @enderror
    </label>
</div>

<label class="mt-6 flex items-center gap-3 text-sm text-foreground-muted">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $unidade->is_active)) class="size-4 rounded border-border bg-surface text-primary">
    Unidade ativa
</label>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('unidades-negocio.index') }}" class="btn-secondary">Cancelar</a>
    <button class="btn-primary">
        <i data-lucide="save" class="size-4"></i>
        Salvar
    </button>
</div>
