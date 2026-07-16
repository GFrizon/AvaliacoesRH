<?php

namespace App\Console\Commands;

use App\Services\AvaliacaoWorkflowService;
use Illuminate\Console\Command;

class EnviarAvaliacoesPendentes extends Command
{
    protected $signature = 'avaliacoes:enviar-pendentes';

    protected $description = 'Envia e-mails das avaliacoes que chegaram no prazo e ainda nao foram notificadas.';

    public function handle(AvaliacaoWorkflowService $workflow): int
    {
        $total = $workflow->enviarPendentesVencidas();

        $this->info("E-mails enviados: {$total}");

        return self::SUCCESS;
    }
}
