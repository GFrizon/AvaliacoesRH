@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Responsáveis"
    title="Novo gestor"
    description="Cadastre o acesso do gestor que responderá avaliações dos colaboradores."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('gestores.store') }}">
        @include('gestores._form')
    </form>
</section>
@endsection
