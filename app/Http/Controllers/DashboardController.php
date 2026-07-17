<?php

namespace App\Http\Controllers;

use App\Enums\AvaliacaoStatus;
use App\Enums\UserRole;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->role === UserRole::Gestor) {
            $pendentes = Avaliacao::query()
                ->with(['colaborador.setor', 'formulario'])
                ->where('gestor_id', $user->id)
                ->where('status', AvaliacaoStatus::Pendente)
                ->orderBy('data_limite')
                ->get();

            return view('dashboards.gestor', [
                'pendentes' => $pendentes,
                'concluidasCount' => Avaliacao::where('gestor_id', $user->id)->where('status', AvaliacaoStatus::Concluida)->count(),
                'atrasadasCount' => $pendentes->where('dias_restantes', '<', 0)->count(),
                'venceHojeCount' => $pendentes->where('dias_restantes', 0)->count(),
            ]);
        }

        $empresaId = $user->empresa_id;

        return view('dashboards.rh', [
            'cards' => [
                'Avaliações agendadas' => Avaliacao::where('empresa_id', $empresaId)->where('status', AvaliacaoStatus::Agendada)->count(),
                'Avaliações pendentes' => Avaliacao::where('empresa_id', $empresaId)->where('status', AvaliacaoStatus::Pendente)->count(),
                'Avaliações concluídas' => Avaliacao::where('empresa_id', $empresaId)->where('status', AvaliacaoStatus::Concluida)->count(),
                'Avaliações canceladas' => Avaliacao::where('empresa_id', $empresaId)->where('status', AvaliacaoStatus::Cancelada)->count(),
                'Gestores' => User::where('empresa_id', $empresaId)->where('role', UserRole::Gestor)->count(),
                'Colaboradores' => Colaborador::where('empresa_id', $empresaId)->count(),
                'Efetivados' => Avaliacao::where('empresa_id', $empresaId)->where('efetivar', true)->count(),
                'Não efetivados' => Avaliacao::where('empresa_id', $empresaId)->where('efetivar', false)->count(),
            ],
            'ultimas' => Avaliacao::with(['colaborador', 'gestor'])
                ->where('empresa_id', $empresaId)
                ->where('status', AvaliacaoStatus::Concluida)
                ->latest('concluida_em')
                ->limit(6)
                ->get(),
            'atrasadas' => Avaliacao::with(['colaborador', 'gestor', 'formulario'])
                ->where('empresa_id', $empresaId)
                ->where('status', AvaliacaoStatus::Pendente)
                ->whereDate('data_limite', '<', now()->toDateString())
                ->orderBy('data_limite')
                ->limit(6)
                ->get(),
            'proximas' => Avaliacao::with(['colaborador', 'gestor', 'formulario'])
                ->where('empresa_id', $empresaId)
                ->where('status', AvaliacaoStatus::Agendada)
                ->whereDate('data_limite', '<=', now()->addDays(15)->toDateString())
                ->orderBy('data_limite')
                ->limit(6)
                ->get(),
            'setores' => Setor::withCount('colaboradores')->where('empresa_id', $empresaId)->get(),
            'porUnidade' => Colaborador::query()
                ->select('unidade_negocio', DB::raw('count(*) as total'))
                ->where('empresa_id', $empresaId)
                ->where('is_active', true)
                ->groupBy('unidade_negocio')
                ->orderByDesc('total')
                ->get(),
            'porStatus' => Avaliacao::query()
                ->select('status', DB::raw('count(*) as total'))
                ->where('empresa_id', $empresaId)
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }
}
