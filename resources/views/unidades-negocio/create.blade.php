@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Estrutura" title="Nova unidade" description="Cadastre uma unidade de negócio para usar nos colaboradores e filtros.">
    <x-slot:actions>
        <a href="{{ route('unidades-negocio.index') }}" class="btn-secondary">Voltar</a>
    </x-slot:actions>
</x-page-header>

<section class="app-card p-5">
    <form method="post" action="{{ route('unidades-negocio.store') }}">
        @include('unidades-negocio._form')
    </form>
</section>
@endsection
