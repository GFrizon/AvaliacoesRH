<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\UnidadeNegocioController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/senha', [PerfilController::class, 'updatePassword'])->name('perfil.password');

    Route::get('/avaliacoes', [AvaliacaoController::class, 'index'])->name('avaliacoes.index');

    Route::middleware('role:rh')->group(function () {
        Route::resource('/colaboradores', ColaboradorController::class)
            ->except('show')
            ->parameters(['colaboradores' => 'colaborador']);
        Route::get('/avaliacoes/create', [AvaliacaoController::class, 'create'])->name('avaliacoes.create');
        Route::post('/avaliacoes', [AvaliacaoController::class, 'store'])->name('avaliacoes.store');
        Route::post('/avaliacoes/{avaliacao}/reenviar-email', [AvaliacaoController::class, 'reenviarEmail'])->name('avaliacoes.reenviar-email');
        Route::resource('/gestores', GestorController::class)
            ->except('show')
            ->parameters(['gestores' => 'gestor']);
        Route::resource('/usuarios', UsuarioController::class)
            ->except('show')
            ->parameters(['usuarios' => 'usuario']);
        Route::resource('/setores', SetorController::class)
            ->except('show')
            ->parameters(['setores' => 'setor']);
        Route::resource('/unidades-negocio', UnidadeNegocioController::class)
            ->except('show');
        Route::get('/formularios', [FormularioController::class, 'index'])->name('formularios.index');
        Route::get('/formularios/{formulario}/edit', [FormularioController::class, 'edit'])->name('formularios.edit');
        Route::put('/formularios/{formulario}', [FormularioController::class, 'update'])->name('formularios.update');
        Route::post('/formularios/{formulario}/perguntas', [FormularioController::class, 'storePergunta'])->name('formularios.perguntas.store');
        Route::delete('/formularios/{formulario}/perguntas/{pergunta}', [FormularioController::class, 'destroyPergunta'])->name('formularios.perguntas.destroy');
        Route::patch('/formularios/{formulario}/perguntas/{pergunta}/restore', [FormularioController::class, 'restorePergunta'])->name('formularios.perguntas.restore');
        Route::get('/formularios/{formulario}', [FormularioController::class, 'show'])->name('formularios.show');
        Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
        Route::get('/relatorios/pdf', [RelatorioController::class, 'pdf'])->name('relatorios.pdf');
        Route::get('/configuracoes', ConfiguracaoController::class)->name('configuracoes.index');
    });

    Route::get('/avaliacoes/{avaliacao}', [AvaliacaoController::class, 'show'])->name('avaliacoes.show');
    Route::post('/avaliacoes/{avaliacao}/enviar', [AvaliacaoController::class, 'submit'])->name('avaliacoes.submit');
});
