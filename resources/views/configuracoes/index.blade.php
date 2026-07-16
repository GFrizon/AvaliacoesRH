@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Operacao"
    title="Configuracoes"
    description="Estado dos pontos que precisam estar corretos para o fluxo automatico funcionar na empresa."
/>

<div class="grid gap-5 lg:grid-cols-2">
    <section class="app-card rounded-xl p-5">
        <div class="mb-4 flex items-center gap-3">
            <div class="grid size-10 place-items-center rounded-lg bg-cyan-400/10 text-cyan-100">
                <i data-lucide="mail" class="size-5"></i>
            </div>
            <div>
                <h3 class="card-title">E-mail</h3>
                <p class="card-description mt-0">Disparo para gestores e avisos ao RH.</p>
            </div>
        </div>
        <div class="space-y-3 text-sm">
            <div class="config-row">
                <span>Mailer</span>
                <strong>{{ $mailer }}</strong>
            </div>
            <div class="config-row">
                <span>Remetente</span>
                <strong>{{ $from }}</strong>
            </div>
            <div class="rounded-lg border {{ $mailer === 'log' ? 'border-amber-300/20 bg-amber-400/10 text-amber-100' : 'border-emerald-300/20 bg-emerald-400/10 text-emerald-100' }} px-3 py-2">
                {{ $mailer === 'log' ? 'Modo log: os e-mails não saem para pessoas reais.' : 'Mailer configurado para envio externo.' }}
            </div>
        </div>
    </section>

    <section class="app-card rounded-xl p-5">
        <div class="mb-4 flex items-center gap-3">
            <div class="grid size-10 place-items-center rounded-lg bg-emerald-400/10 text-emerald-100">
                <i data-lucide="clock-3" class="size-5"></i>
            </div>
            <div>
                <h3 class="card-title">Automacao</h3>
                <p class="card-description mt-0">Rotina que verifica prazos e envia lembretes.</p>
            </div>
        </div>
        <div class="space-y-3 text-sm">
            <div class="rounded-lg bg-slate-950/45 px-3 py-2 text-slate-300">
                <code>php artisan avaliacoes:enviar-pendentes</code>
            </div>
            <div class="rounded-lg bg-slate-950/45 px-3 py-2 text-slate-300">
                <code>php artisan schedule:work</code>
            </div>
            <p class="card-description">Em servidor de producao, essa rotina deve ficar ativa continuamente ou via cron do Laravel.</p>
        </div>
    </section>
</div>

<section class="app-card mt-6 rounded-xl p-5">
    <h3 class="section-title">Fluxo operacional esperado</h3>
    <div class="mt-4 grid gap-3 md:grid-cols-4">
        @foreach (['RH cadastra colaborador com gestor e modelo', 'Sistema agenda 90 dias, 6 meses e 1 ano', 'No prazo, e-mail vai ao gestor e lembretes continuam', 'RH recebe o resultado e futuras etapas cancelam se não efetivar'] as $step)
            <div class="rounded-lg border border-white/10 bg-slate-950/45 p-4 text-sm text-slate-300">{{ $step }}</div>
        @endforeach
    </div>
</section>
@endsection
