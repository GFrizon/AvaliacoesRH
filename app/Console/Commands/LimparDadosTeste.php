<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimparDadosTeste extends Command
{
    protected $signature = 'avaliacoes:limpar-testes {--force : Executa sem pedir confirmacao em ambiente de producao}';

    protected $description = 'Remove dados operacionais de teste mantendo usuarios, formularios, setores, unidades e configuracoes.';

    public function handle(): int
    {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->warn('Este comando apaga avaliacoes, respostas, colaboradores, logs de e-mail, fila e sessoes.');

            if (! $this->confirm('Deseja continuar?')) {
                $this->info('Limpeza cancelada.');

                return self::FAILURE;
            }
        }

        $tables = [
            'notifications',
            'email_logs',
            'respostas',
            'avaliacoes',
            'colaboradores',
            'jobs',
            'failed_jobs',
            'job_batches',
            'sessions',
            'password_reset_tokens',
        ];

        $deleted = [];

        DB::transaction(function () use ($tables, &$deleted): void {
            foreach ($tables as $table) {
                $deleted[$table] = DB::table($table)->count();
                DB::table($table)->delete();
            }
        });

        $this->newLine();
        $this->info('Dados de teste removidos:');

        foreach ($deleted as $table => $count) {
            $this->line("- {$table}: {$count}");
        }

        $this->newLine();
        $this->comment('Mantidos: usuarios, empresa, setores, unidades, formularios, perguntas e configuracoes.');

        return self::SUCCESS;
    }
}
