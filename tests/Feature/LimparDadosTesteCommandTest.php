<?php

namespace Tests\Feature;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use App\Enums\FormularioTipo;
use App\Enums\PerguntaTipo;
use App\Enums\UserRole;
use App\Models\Avaliacao;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Pergunta;
use App\Models\Resposta;
use App\Models\Setor;
use App\Models\UnidadeNegocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LimparDadosTesteCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_removes_operational_test_data_and_keeps_base_records(): void
    {
        $empresa = Empresa::create(['nome' => 'Bakof Tec']);
        $setor = Setor::create(['empresa_id' => $empresa->id, 'nome' => 'Operacoes']);
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
        $pergunta = Pergunta::create([
            'formulario_id' => $formulario->id,
            'titulo' => 'Pontualidade',
            'tipo' => PerguntaTipo::TextoLongo,
            'ordem' => 1,
        ]);
        $colaborador = Colaborador::create([
            'empresa_id' => $empresa->id,
            'setor_id' => $setor->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'nome' => 'Colaborador Teste',
            'cargo' => 'Analista',
        ]);
        $avaliacao = Avaliacao::create([
            'empresa_id' => $empresa->id,
            'colaborador_id' => $colaborador->id,
            'gestor_id' => $gestor->id,
            'formulario_id' => $formulario->id,
            'ciclo' => AvaliacaoCiclo::NoventaDias,
            'status' => AvaliacaoStatus::Concluida,
            'data_limite' => now(),
            'efetivar' => true,
        ]);
        Resposta::create([
            'avaliacao_id' => $avaliacao->id,
            'pergunta_id' => $pergunta->id,
            'valor' => ['texto' => 'Teste'],
        ]);
        DB::table('email_logs')->insert([
            'empresa_id' => $empresa->id,
            'avaliacao_id' => $avaliacao->id,
            'tipo' => 'teste',
            'destinatario' => 'teste@example.com',
            'status' => 'enviado',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'available_at' => time(),
            'created_at' => time(),
        ]);

        $this->artisan('avaliacoes:limpar-testes', ['--force' => true])
            ->assertExitCode(0);

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', ['id' => $rh->id]);
        $this->assertDatabaseCount('empresas', 1);
        $this->assertDatabaseCount('setores', 1);
        $this->assertDatabaseCount('unidades_negocio', 1);
        $this->assertDatabaseCount('formularios', 1);
        $this->assertDatabaseCount('perguntas', 1);

        $this->assertDatabaseCount('colaboradores', 0);
        $this->assertDatabaseCount('avaliacoes', 0);
        $this->assertDatabaseCount('respostas', 0);
        $this->assertDatabaseCount('email_logs', 0);
        $this->assertDatabaseCount('jobs', 0);
    }
}
