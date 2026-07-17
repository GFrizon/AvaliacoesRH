@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Pessoas"
    title="Editar colaborador"
    description="Atualize vínculo, unidade, gestor e dados de contato."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('colaboradores.update', $colaborador) }}">
        @method('put')
        @include('colaboradores._form')
    </form>
</section>
@endsection
