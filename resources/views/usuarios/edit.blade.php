@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Acessos e alertas"
    title="Editar usuário"
    description="Atualize dados de acesso, perfil e e-mail de notificação."
/>

<section class="app-card p-5">
    <form method="post" action="{{ route('usuarios.update', $usuario) }}">
        @method('put')
        @include('usuarios._form')
    </form>
</section>
@endsection
