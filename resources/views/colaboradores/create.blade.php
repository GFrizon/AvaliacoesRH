@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Pessoas"
    title="Novo colaborador"
    description="Cadastre o colaborador e já deixe os ciclos de avaliação preparados."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('colaboradores.store') }}">
        @include('colaboradores._form')
    </form>
</section>
@endsection
