@extends('layouts.app')

@section('content')
<div class="mb-8">
    <p class="text-sm text-zinc-400">Responsáveis</p>
    <h2 class="mt-1 text-3xl font-semibold">Editar gestor</h2>
</div>

<section class="rounded-xl border border-white/10 bg-zinc-900/70 p-5">
    <form method="post" action="{{ route('gestores.update', $gestor) }}">
        @method('put')
        @include('gestores._form')
    </form>
</section>
@endsection
