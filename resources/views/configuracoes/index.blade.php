@extends('layouts.app')

@section('content')
<x-page-header
    eyebrow="Operação"
    title="Configurações"
    description="Estado dos pontos que precisam estar corretos para e-mails, filas e rotinas automáticas."
/>

<div class="grid gap-5 lg:grid-cols-3">
    <section class="app-card p-5">
        <div class="mb-4 flex items-center gap-3">
            <div class="metric-icon bg-info-background text-info">
                <i data-lucide="mail" class="size-5"></i>
            </div>
            <div>
                <h3 class="card-title">E-mail</h3>
                <p class="card-description mt-0">Disparo para gestores e avisos ao RH.</p>
            </div>
        </div>
        <div class="space-y-3 text-sm">
            <div class="config-row"><span>Mailer</span><strong>{{ $mailer }}</strong></div>
            <div class="config-row"><span>Remetente</span><strong>{{ $from }}</strong></div>
            <div class="rounded-lg border {{ $mailer === 'log' ? 'border-warning bg-warning-background text-warning' : 'border-success bg-success-background text-success' }} px-3 py-2">
                {{ $mailer === 'log' ? 'Modo log: os e-mails não saem para pessoas reais.' : 'Mailer configurado para envio externo.' }}
            </div>
        </div>
    </section>

    <section class="app-card p-5">
        <div class="mb-4 flex items-center gap-3">
            <div class="metric-icon bg-warning-background text-warning">
                <i data-lucide="list-checks" class="size-5"></i>
            </div>
            <div>
                <h3 class="card-title">Fila de envio</h3>
                <p class="card-description mt-0">E-mails aguardam aqui até o cron processar.</p>
            </div>
        </div>
        <div class="space-y-3 text-sm">
            <div class="config-row"><span>Conexão</span><strong>{{ $queueConnection }}</strong></div>
            <div class="config-row"><span>Pendentes</span><strong>{{ $jobsPendentes }}</strong></div>
            <div class="config-row"><span>Falhados</span><strong class="{{ $jobsFalhados > 0 ? 'text-danger' : '' }}">{{ $jobsFalhados }}</strong></div>
        </div>
    </section>

    <section class="app-card p-5">
        <div class="mb-4 flex items-center gap-3">
            <div class="metric-icon bg-success-background text-success">
                <i data-lucide="clock-3" class="size-5"></i>
            </div>
            <div>
                <h3 class="card-title">Automação</h3>
                <p class="card-description mt-0">Cron obrigatório em produção.</p>
            </div>
        </div>
        <div class="space-y-3 text-sm">
            <div class="rounded-lg border border-border bg-background p-3">
                <code class="text-xs text-foreground-muted">* * * * * cd /home/bkteccom/avaliacoes && php artisan queue:work --stop-when-empty --tries=3 --timeout=120 >> /home/bkteccom/avaliacoes/storage/logs/queue.log 2>&1</code>
            </div>
            <div class="rounded-lg border border-border bg-background p-3">
                <code class="text-xs text-foreground-muted">* * * * * cd /home/bkteccom/avaliacoes && php artisan schedule:run >> /home/bkteccom/avaliacoes/storage/logs/schedule.log 2>&1</code>
            </div>
        </div>
    </section>
</div>

<section class="app-card mt-6 p-5">
    <h3 class="section-title">Fluxo operacional esperado</h3>
    <div class="mt-4 grid gap-3 md:grid-cols-4">
        @foreach (['RH cadastra colaborador com gestor e modelo', 'Sistema agenda 90 dias, 6 meses e 1 ano', 'No prazo, e-mail vai ao gestor e lembretes continuam', 'RH recebe o resultado e futuras etapas cancelam se não efetivar'] as $step)
            <div class="rounded-lg border border-border bg-background p-4 text-sm text-foreground-muted">{{ $step }}</div>
        @endforeach
    </div>
</section>

<section class="app-card mt-6 p-5">
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h3 class="section-title">Últimos e-mails enfileirados</h3>
            <p class="card-description mt-1">Mostra quando o sistema colocou mensagens na fila de envio.</p>
        </div>
        <x-badge variant="info">{{ $ultimosEmails->count() }}</x-badge>
    </div>

    <div class="desktop-table table-shell">
        <table class="w-full text-left text-sm">
            <thead>
                <tr>
                    <th class="px-4 py-3">Quando</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Destinatário</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($ultimosEmails as $email)
                    <tr>
                        <td class="px-4 py-4 table-text">{{ $email->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-4 table-title">{{ str_replace('_', ' ', $email->tipo) }}</td>
                        <td class="px-4 py-4 table-text">{{ $email->destinatario }}</td>
                        <td class="px-4 py-4"><span class="status-pill status-info">{{ $email->status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-foreground-muted">Nenhum e-mail foi enfileirado ainda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
