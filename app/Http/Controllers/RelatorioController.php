<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\User;
use App\Support\UnidadesNegocio;
use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\UserRole;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function index(Request $request): View
    {
        $empresaId = $request->user()->empresa_id;

        return view('relatorios.index', [
            'avaliacoes' => $this->query($request)->paginate(20)->withQueryString(),
            'gestores' => User::where('empresa_id', $empresaId)->where('role', UserRole::Gestor)->orderBy('name')->get(),
            'unidadesNegocio' => UnidadesNegocio::options($empresaId),
            'ciclos' => AvaliacaoCiclo::cases(),
            'statusOptions' => AvaliacaoStatus::cases(),
        ]);
    }

    public function pdf(Request $request): Response
    {
        $pdf = Pdf::loadView('relatorios.pdf', [
            'avaliacoes' => $this->query($request)->get(),
            'filtros' => $request->only(['inicio', 'fim', 'status', 'gestor_id', 'unidade_negocio', 'ciclo']),
        ]);

        return $pdf->download('relatorio-avaliacoes.pdf');
    }

    private function query(Request $request)
    {
        return Avaliacao::with(['colaborador.setor', 'gestor', 'formulario'])
            ->where('empresa_id', $request->user()->empresa_id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('gestor_id'), fn ($query) => $query->where('gestor_id', $request->integer('gestor_id')))
            ->when($request->filled('ciclo'), fn ($query) => $query->where('ciclo', $request->string('ciclo')->toString()))
            ->when($request->filled('unidade_negocio'), function ($query) use ($request): void {
                $query->whereHas('colaborador', fn ($query) => $query->where('unidade_negocio', $request->string('unidade_negocio')->toString()));
            })
            ->when($request->filled('inicio'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('inicio')))
            ->when($request->filled('fim'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('fim')))
            ->latest();
    }
}
