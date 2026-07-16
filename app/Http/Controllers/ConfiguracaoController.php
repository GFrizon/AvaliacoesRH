<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    public function __invoke(): View
    {
        return view('configuracoes.index', [
            'mailer' => config('mail.default'),
            'from' => config('mail.from.address'),
            'appUrl' => config('app.url'),
        ]);
    }
}
