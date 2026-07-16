<?php

namespace Tests\Feature;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\FormularioTipo;
use App\Enums\PerguntaTipo;
use App\Enums\UserRole;
use App\Mail\AvaliacaoPendenteMail;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Pergunta;
use App\Models\Setor;
use App\Models\UnidadeNegocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AvaliacaoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rh_can_create_avaliacao(): void
    {
        Mail::fake();

        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Administrativo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $colaborador = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'nome' => 'Ana Beatriz',
            'cargo' => 'Assistente Administrativo',
            'data_admissao' => '2026-01-01',
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliacao ADM',
            'tipo' => FormularioTipo::Administrativo,
        ]);

        Pergunta::create([
            'formulario_id' => $formulario->id,
            'titulo' => 'Pontualidade',
            'tipo' => PerguntaTipo::TextoLongo,
            'ordem' => 1,
        ]);

        $response = $this->actingAs($rh)->post(route('avaliacoes.store'), [
            'colaborador_mode' => 'existing',
            'colaborador_id' => $colaborador->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias->value,
        ]);

        $avaliacao = Avaliacao::first();

        $response->assertRedirect(route('avaliacoes.show', $avaliacao));
        $this->assertDatabaseHas('avaliacoes', [
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaborador->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias->value,
            'status' => 'pendente',
        ]);
        $this->assertSame('2026-04-01', $avaliacao->data_limite->toDateString());
        Mail::assertQueued(AvaliacaoPendenteMail::class, fn ($mail) => $mail->hasTo($gestor->email));
    }

    public function test_rh_can_create_avaliacao_with_quick_colaborador(): void
    {
        Mail::fake();

        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        UnidadeNegocio::create(['empresa_id' => $empresa->id, 'nome' => 'Bakof Matriz']);
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

        $response = $this->actingAs($rh)->post(route('avaliacoes.store'), [
            'colaborador_mode' => 'new',
            'novo_colaborador_nome' => 'Joao Rapido',
            'novo_colaborador_cpf' => '123.456.789-00',
            'novo_colaborador_unidade_negocio' => 'Bakof Matriz',
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias->value,
            'data_limite' => '2026-10-01',
        ]);

        $colaborador = Colaborador::firstWhere('cpf', '123.456.789-00');
        $avaliacao = Avaliacao::first();

        $response->assertRedirect(route('avaliacoes.show', $avaliacao));
        $this->assertDatabaseHas('colaboradores', [
            'empresa_id' => $empresa->id,
            'nome' => 'Joao Rapido',
            'cpf' => '123.456.789-00',
            'unidade_negocio' => 'Bakof Matriz',
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'cargo' => 'Não informado',
        ]);
        $this->assertDatabaseHas('setores', [
            'empresa_id' => $empresa->id,
            'nome' => 'Não informado',
        ]);
        $this->assertSame($colaborador->id, $avaliacao->colaborador_id);
        $this->assertSame('2026-10-01', $avaliacao->data_limite->toDateString());
        $this->assertSame('pendente', $avaliacao->status->value);
        Mail::assertQueued(AvaliacaoPendenteMail::class, fn ($mail) => $mail->hasTo($gestor->email));
    }

    public function test_rh_can_resend_pending_evaluation_email(): void
    {
        Mail::fake();

        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Administrativo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $colaborador = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'nome' => 'Ana Beatriz',
            'cargo' => 'Assistente Administrativo',
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliação ADM',
            'tipo' => FormularioTipo::Administrativo,
        ]);
        $avaliacao = Avaliacao::create([
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaborador->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias,
            'status' => AvaliacaoStatus::Pendente,
            'data_limite' => now(),
        ]);

        $this->actingAs($rh)
            ->post(route('avaliacoes.reenviar-email', $avaliacao))
            ->assertSessionHas('status', 'E-mail colocado na fila de envio.');

        Mail::assertQueued(AvaliacaoPendenteMail::class, fn ($mail) => $mail->hasTo($gestor->email));
        $this->assertNotNull($avaliacao->refresh()->ultimo_lembrete_em);
    }

    public function test_gestor_cannot_resend_evaluation_email(): void
    {
        Mail::fake();

        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Administrativo']);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Gestor,
        ]);
        $colaborador = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'nome' => 'Ana Beatriz',
            'cargo' => 'Assistente Administrativo',
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliação ADM',
            'tipo' => FormularioTipo::Administrativo,
        ]);
        $avaliacao = Avaliacao::create([
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaborador->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias,
            'status' => AvaliacaoStatus::Pendente,
            'data_limite' => now(),
        ]);

        $this->actingAs($gestor)
            ->post(route('avaliacoes.reenviar-email', $avaliacao))
            ->assertForbidden();

        Mail::assertNothingQueued();
    }
}
