@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Áreas"
    title="Editar setor"
    description="Ajuste o nome, descrição e disponibilidade do setor."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('setores.update', $setor) }}">
        @method('put')
        @include('setores._form')
    </form>
</section>
@endsection
