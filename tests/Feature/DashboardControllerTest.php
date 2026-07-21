<?php

namespace Tests\Feature;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\FormularioTipo;
use App\Enums\UserRole;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_gestor_dashboard_shows_scheduled_and_pending_evaluations(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'TI']);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $colaboradorAgendado = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'nome' => 'Teste GABRIEL',
            'cargo' => 'Testador',
        ]);
        $colaboradorPendente = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'nome' => 'Ana Pendente',
            'cargo' => 'Analista',
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Formulario Comercial',
            'tipo' => FormularioTipo::ComercialEngenharia,
        ]);

        Avaliacao::create([
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaboradorAgendado->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias,
            'status' => AvaliacaoStatus::Agendada,
            'data_limite' => now()->addDays(90),
        ]);

        Avaliacao::create([
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaboradorPendente->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::SeisMeses,
            'status' => AvaliacaoStatus::Pendente,
            'data_limite' => now(),
        ]);

        $this->actingAs($gestor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pendentes')
            ->assertSee('Agendadas')
            ->assertSee('Teste GABRIEL')
            ->assertSee('Ana Pendente');
    }
}
