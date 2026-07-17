@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Responsáveis"
    title="Editar gestor"
    description="Atualize os dados de acesso, contato e status do gestor."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('gestores.update', $gestor) }}">
        @method('put')
        @include('gestores._form')
    </form>
</section>
@endsection
