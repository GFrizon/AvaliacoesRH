@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Áreas"
    title="Novo setor"
    description="Crie um setor para organizar colaboradores e relatórios."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('setores.store') }}">
        @include('setores._form')
    </form>
</section>
@endsection
