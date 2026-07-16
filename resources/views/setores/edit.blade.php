@extends('layouts.app')

@section('content')
<div class="mb-8">
    <p class="text-sm text-zinc-400">Areas</p>
    <h2 class="mt-1 text-3xl font-semibold">Editar setor</h2>
</div>

<section class="rounded-xl border border-white/10 bg-zinc-900/70 p-5">
    <form method="post" action="{{ route('setores.update', $setor) }}">
        @method('put')
        @include('setores._form')
    </form>
</section>
@endsection
