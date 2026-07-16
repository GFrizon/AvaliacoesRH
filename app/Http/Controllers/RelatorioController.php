<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function index(Request $request): View
    {
        return view('relatorios.index', [
            'avaliacoes' => $this->query($request)->paginate(20)->withQueryString(),
        ]);
    }

    public function pdf(Request $request): Response
    {
        $pdf = Pdf::loadView('relatorios.pdf', [
            'avaliacoes' => $this->query($request)->get(),
        ]);

        return $pdf->download('relatorio-avaliacoes.pdf');
    }

    private function query(Request $request)
    {
        return Avaliacao::with(['colaborador.setor', 'gestor'])
            ->where('empresa_id', $request->user()->empresa_id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('inicio'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('inicio')))
            ->when($request->filled('fim'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('fim')))
            ->latest();
    }
}
