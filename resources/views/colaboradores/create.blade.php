@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
    <div>
        <p class="text-sm text-zinc-400">Pessoas</p>
        <h2 class="mt-1 text-3xl font-semibold">Novo colaborador</h2>
    </div>
</div>

<section class="rounded-xl border border-white/10 bg-zinc-900/70 p-5">
    <form method="post" action="{{ route('colaboradores.store') }}">
        @include('colaboradores._form')
    </form>
</section>
@endsection
