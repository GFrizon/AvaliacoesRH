<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    public function __invoke(): View
    {
        return view('configuracoes.index', [
            'mailer' => config('mail.default'),
            'from' => config('mail.from.address'),
            'appUrl' => config('app.url'),
            'queueConnection' => config('queue.default'),
            'jobsPendentes' => DB::table('jobs')->count(),
            'jobsFalhados' => DB::table('failed_jobs')->count(),
            'ultimoJob' => DB::table('jobs')->orderByDesc('id')->first(),
            'ultimoFalhado' => DB::table('failed_jobs')->orderByDesc('id')->first(),
            'ultimosEmails' => EmailLog::query()
                ->where('empresa_id', request()->user()->empresa_id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
