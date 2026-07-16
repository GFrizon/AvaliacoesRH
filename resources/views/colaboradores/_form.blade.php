@csrf

<div class="grid gap-5 md:grid-cols-2">
    <label class="block">
        <span class="text-sm text-zinc-300">Nome</span>
        <input name="nome" value="{{ old('nome', $colaborador->nome) }}" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('nome') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Cargo</span>
        <input name="cargo" value="{{ old('cargo', $colaborador->cargo) }}" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('cargo') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">CPF</span>
        <input name="cpf" value="{{ old('cpf', $colaborador->cpf) }}" placeholder="000.000.000-00" class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('cpf') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Unidade de negócio</span>
        <select name="unidade_negocio" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
            <option value="">Selecione</option>
            @foreach ($unidadesNegocio as $unidade)
                <option value="{{ $unidade }}" @selected(old('unidade_negocio', $colaborador->unidade_negocio) === $unidade)>{{ $unidade }}</option>
            @endforeach
        </select>
        @error('unidade_negocio') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Setor</span>
        <select name="setor_id" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
            <option value="">Selecione</option>
            @foreach ($setores as $setor)
                <option value="{{ $setor->id }}" @selected((int) old('setor_id', $colaborador->setor_id) === $setor->id)>{{ $setor->nome }}</option>
            @endforeach
        </select>
        @error('setor_id') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Gestor responsável</span>
        <select name="gestor_id" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
            <option value="">Selecione</option>
            @foreach ($gestores as $gestor)
                <option value="{{ $gestor->id }}" @selected((int) old('gestor_id', $colaborador->gestor_id) === $gestor->id)>{{ $gestor->name }}</option>
            @endforeach
        </select>
        @error('gestor_id') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Modelo de avaliação</span>
        <select name="formulario_id" required class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
            <option value="">Selecione</option>
            @foreach ($formularios as $formulario)
                <option value="{{ $formulario->id }}" @selected((int) old('formulario_id', $colaborador->formulario_id) === $formulario->id)>
                    {{ $formulario->tipo->label() }} - {{ $formulario->nome }}
                </option>
            @endforeach
        </select>
        @error('formulario_id') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Data de admissão</span>
        <input type="date" name="data_admissao" value="{{ old('data_admissao', optional($colaborador->data_admissao)->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('data_admissao') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">E-mail</span>
        <input type="email" name="email" value="{{ old('email', $colaborador->email) }}" class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('email') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>

    <label class="block">
        <span class="text-sm text-zinc-300">Telefone</span>
        <input name="telefone" value="{{ old('telefone', $colaborador->telefone) }}" class="mt-2 w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-sm text-white outline-none transition focus:border-blue-400">
        @error('telefone') <span class="mt-1 block text-xs text-red-300">{{ $message }}</span> @enderror
    </label>
</div>

<label class="mt-6 flex items-center gap-3 text-sm text-zinc-300">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $colaborador->is_active)) class="size-4 rounded border-white/10 bg-zinc-950 text-blue-500">
    Colaborador ativo
</label>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('colaboradores.index') }}" class="btn-secondary">
        Cancelar
    </a>
    <button class="btn-primary">
        <i data-lucide="save" class="size-4"></i>
        Salvar
    </button>
</div>
