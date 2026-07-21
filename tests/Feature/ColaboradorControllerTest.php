<?php

namespace Tests\Feature;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\FormularioTipo;
use App\Enums\UserRole;
use App\Mail\AvaliacoesAgendadasMail;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Setor;
use App\Models\UnidadeNegocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ColaboradorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rh_can_view_colaboradores(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Produto']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliacao ADM',
            'tipo' => FormularioTipo::Administrativo,
        ]);

        Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'nome' => 'Ana Beatriz',
            'cargo' => 'Product Designer',
        ]);

        $this->actingAs($rh)
            ->get(route('colaboradores.index'))
            ->assertOk()
            ->assertSee('Ana Beatriz')
            ->assertSee('Product Designer');
    }

    public function test_rh_can_create_colaborador(): void
    {
        Mail::fake();

        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Operacoes']);
        UnidadeNegocio::create(['empresa_id' => $empresa->id, 'nome' => 'Bakof Tec']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliacao Industria',
            'tipo' => FormularioTipo::Industria,
        ]);

        $this->actingAs($rh)
            ->post(route('colaboradores.store'), [
                'setor_id' => $setor->id,
                'gestor_id' => $gestor->id,
                'formulario_id' => $formulario->id,
                'nome' => 'Joao Victor',
                'cpf' => '987.654.321-00',
                'unidade_negocio' => 'Bakof Tec',
                'email' => 'joao@example.com',
                'telefone' => '11999990000',
                'cargo' => 'Analista de Operacoes',
                'data_admissao' => '2026-07-01',
                'is_active' => '1',
            ])
            ->assertRedirect(route('colaboradores.index'));

        $this->assertDatabaseHas('colaboradores', [
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'nome' => 'Joao Victor',
            'cpf' => '987.654.321-00',
            'unidade_negocio' => 'Bakof Tec',
            'email' => 'joao@example.com',
            'is_active' => true,
        ]);
        $this->assertSame(3, Avaliacao::count());
        $this->assertSame(3, Avaliacao::where('status', AvaliacaoStatus::Agendada)->count());
        Mail::assertSent(AvaliacoesAgendadasMail::class, fn ($mail) => $mail->hasTo($gestor->email));
    }

    public function test_updating_admission_date_recalculates_open_evaluation_deadlines(): void
    {
        Mail::fake();

        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Operacoes']);
        UnidadeNegocio::create(['empresa_id' => $empresa->id, 'nome' => 'Bakof Tec']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliacao Industria',
            'tipo' => FormularioTipo::Industria,
        ]);
        $colaborador = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'nome' => 'Joao Victor',
            'cpf' => '987.654.321-00',
            'unidade_negocio' => 'Bakof Tec',
            'email' => 'joao@example.com',
            'telefone' => '11999990000',
            'cargo' => 'Analista de Operacoes',
            'data_admissao' => '2026-07-01',
            'is_active' => true,
        ]);

        app(\App\Services\AvaliacaoWorkflowService::class)->garantirAgenda($colaborador);
        $this->assertSame('2026-09-29', Avaliacao::where('ciclo', AvaliacaoCiclo::NoventaDias)->first()->data_limite->toDateString());

        $this->actingAs($rh)
            ->put(route('colaboradores.update', $colaborador), [
                'setor_id' => $setor->id,
                'gestor_id' => $gestor->id,
                'formulario_id' => $formulario->id,
                'nome' => 'Joao Victor',
                'cpf' => '987.654.321-00',
                'unidade_negocio' => 'Bakof Tec',
                'email' => 'joao@example.com',
                'telefone' => '11999990000',
                'cargo' => 'Analista de Operacoes',
                'data_admissao' => '2026-08-01',
                'is_active' => '1',
            ])
            ->assertRedirect(route('colaboradores.index'));

        $noventaDias = Avaliacao::where('ciclo', AvaliacaoCiclo::NoventaDias)->first()->refresh();

        $this->assertSame('2026-10-30', $noventaDias->data_limite->toDateString());
        $this->assertSame(AvaliacaoStatus::Agendada, $noventaDias->status);
        $this->assertSame('2027-02-01', Avaliacao::where('ciclo', AvaliacaoCiclo::SeisMeses)->first()->refresh()->data_limite->toDateString());
        $this->assertSame('2027-08-01', Avaliacao::where('ciclo', AvaliacaoCiclo::UmAno)->first()->refresh()->data_limite->toDateString());

        Mail::assertSent(AvaliacoesAgendadasMail::class, fn ($mail) => $mail->hasTo($gestor->email) && $mail->reagendada);
        $this->assertDatabaseHas('email_logs', [
            'destinatario' => $gestor->email,
            'tipo' => 'avaliacoes_reagendadas',
            'status' => 'enviado',
        ]);
    }
}
