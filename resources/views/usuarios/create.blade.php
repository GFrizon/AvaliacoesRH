@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Acessos e alertas"
    title="Novo usuário"
    description="Cadastre um usuário RH ou gestor e informe o e-mail que receberá os alertas."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('usuarios.store') }}">
        @include('usuarios._form')
    </form>
</section>
@endsection
