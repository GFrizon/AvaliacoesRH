@extends('layouts.app')

@section('content')
<section class="rounded-xl border border-white/10 bg-zinc-900/70 p-8">
    <p class="text-sm text-zinc-400">Modulo</p>
    <h2 class="mt-2 text-3xl font-semibold">{{ $title }}</h2>
    <p class="mt-3 max-w-2xl text-zinc-400">{{ $description }}</p>
    <div class="mt-8 grid gap-4 md:grid-cols-3">
        <div class="h-24 rounded-lg border border-white/10 bg-zinc-950/80"></div>
        <div class="h-24 rounded-lg border border-white/10 bg-zinc-950/80"></div>
        <div class="h-24 rounded-lg border border-white/10 bg-zinc-950/80"></div>
    </div>
</section>
@endsection
