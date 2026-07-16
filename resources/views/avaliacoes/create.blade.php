@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Fluxo de desempenho"
    title="Nova avaliação"
    description="Crie a avaliação escolhendo um colaborador existente ou cadastrando rapidamente os dados essenciais."
>
    <x-slot:actions>
        <a href="{{ route('avaliacoes.index') }}" class="btn-secondary">Voltar</a>
    </x-slot:actions>
</x-page-header>

<section class="app-card p-5">
    <form
        method="post"
        action="{{ route('avaliacoes.store') }}"
        class="space-y-6"
        x-data="{ mode: @js(old('colaborador_mode', 'new')) }"
    >
        @csrf

        <div>
            <p class="form-label mb-2">Colaborador</p>
            <div class="segmented-control">
                <label :class="{ 'is-active': mode === 'new' }">
                    <input type="radio" name="colaborador_mode" value="new" class="sr-only" x-model="mode">
                    Novo colaborador
                </label>
                <label :class="{ 'is-active': mode === 'existing' }">
                    <input type="radio" name="colaborador_mode" value="existing" class="sr-only" x-model="mode">
                    Já cadastrado
                </label>
            </div>
            @error('colaborador_mode') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div x-show="mode === 'new'" x-cloak class="quick-create-panel">
            <div class="mb-4">
                <h3 class="card-title">Cadastro rapido</h3>
                <p class="card-description">O sistema cria o colaborador com dados mínimos e vincula gestor/modelo desta avaliação.</p>
            </div>
            <div class="grid gap-5 md:grid-cols-3">
                <label class="block">
                    <span class="form-label">Nome do colaborador</span>
                    <input name="novo_colaborador_nome" value="{{ old('novo_colaborador_nome') }}" class="app-input mt-2 w-full px-3 py-2 text-sm" :required="mode === 'new'">
                    @error('novo_colaborador_nome') <span class="form-error">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="form-label">CPF</span>
                    <input name="novo_colaborador_cpf" value="{{ old('novo_colaborador_cpf') }}" placeholder="000.000.000-00" class="app-input mt-2 w-full px-3 py-2 text-sm" :required="mode === 'new'">
                    @error('novo_colaborador_cpf') <span class="form-error">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="form-label">Unidade de negócio</span>
                    <select name="novo_colaborador_unidade_negocio" class="app-input mt-2 w-full px-3 py-2 text-sm" :required="mode === 'new'">
                        <option value="">Selecione</option>
                        @foreach ($unidadesNegocio as $unidade)
                            <option value="{{ $unidade }}" @selected(old('novo_colaborador_unidade_negocio') === $unidade)>{{ $unidade }}</option>
                        @endforeach
                    </select>
                    @error('novo_colaborador_unidade_negocio') <span class="form-error">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>

        <div x-show="mode === 'existing'" x-cloak>
            <label class="block">
                <span class="form-label">Colaborador cadastrado</span>
                <select name="colaborador_id" class="app-input mt-2 w-full px-3 py-2 text-sm" :required="mode === 'existing'">
                    <option value="">Selecione</option>
                    @foreach ($colaboradores as $colaborador)
                        <option value="{{ $colaborador->id }}" @selected((int) old('colaborador_id') === $colaborador->id)>
                            {{ $colaborador->nome }} - {{ $colaborador->cargo }} / {{ $colaborador->setor->nome }}
                            @if ($colaborador->unidade_negocio)
                                - {{ $colaborador->unidade_negocio }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('colaborador_id') <span class="form-error">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <label class="block">
                <span class="form-label">Gestor responsável</span>
                <select name="gestor_id" required class="app-input mt-2 w-full px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach ($gestores as $gestor)
                        <option value="{{ $gestor->id }}" @selected((int) old('gestor_id') === $gestor->id)>{{ $gestor->name }}</option>
                    @endforeach
                </select>
                @error('gestor_id') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="form-label">Modelo de formulario</span>
                <select name="formulario_id" required class="app-input mt-2 w-full px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach ($formularios as $formulario)
                        <option value="{{ $formulario->id }}" @selected((int) old('formulario_id') === $formulario->id)>
                            {{ $formulario->tipo->label() }} - {{ $formulario->nome }}
                        </option>
                    @endforeach
                </select>
                @error('formulario_id') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="form-label">Ciclo</span>
                <select name="ciclo" required class="app-input mt-2 w-full px-3 py-2 text-sm">
                    @foreach ($ciclos as $ciclo)
                        <option value="{{ $ciclo->value }}" @selected(old('ciclo') === $ciclo->value)>{{ $ciclo->label() }}</option>
                    @endforeach
                </select>
                @error('ciclo') <span class="form-error">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="form-label">Prazo</span>
                <input type="date" name="data_limite" value="{{ old('data_limite') }}" class="app-input mt-2 w-full px-3 py-2 text-sm">
                <span class="mt-1 block text-xs text-foreground-muted">Se ficar em branco, o sistema calcula pela data de hoje e pelo ciclo escolhido.</span>
                @error('data_limite') <span class="form-error">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('avaliacoes.index') }}" class="btn-secondary">Cancelar</a>
            <button class="btn-primary">
                <i data-lucide="clipboard-plus" class="size-4"></i>
                Criar avaliação
            </button>
        </div>
    </form>
</section>
@endsection
