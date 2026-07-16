@extends('layouts.app')

@section('content')
<x-page-header eyebrow="Estrutura" title="Editar unidade" description="Atualize o nome, descrição e status da unidade de negócio.">
    <x-slot:actions>
        <a href="{{ route('unidades-negocio.index') }}" class="btn-secondary">Voltar</a>
    </x-slot:actions>
</x-page-header>

<section class="app-card p-5">
    <form method="post" action="{{ route('unidades-negocio.update', $unidade) }}">
        @method('put')
        @include('unidades-negocio._form')
    </form>
</section>
@endsection
