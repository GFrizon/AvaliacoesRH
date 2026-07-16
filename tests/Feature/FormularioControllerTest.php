<?php

namespace Tests\Feature;

use App\Enums\FormularioTipo;
use App\Enums\PerguntaTipo;
use App\Enums\UserRole;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Pergunta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormularioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rh_can_edit_formulario_and_question(): void
    {
        [$rh, $formulario, $pergunta] = $this->makeFormulario();

        $response = $this->actingAs($rh)->put(route('formularios.update', $formulario), [
            'nome' => 'Avaliacao Operacional',
            'descricao' => 'Modelo revisado pelo RH.',
            'tipo' => FormularioTipo::Industria->value,
            'is_active' => '1',
            'perguntas' => [
                $pergunta->id => [
                    'titulo' => 'Como esta a entrega?',
                    'descricao' => 'Considere qualidade, prazo e postura.',
                    'tipo' => PerguntaTipo::TextoLongo->value,
                    'ordem' => 2,
                    'obrigatoria' => '1',
                ],
            ],
        ]);

        $response->assertRedirect(route('formularios.edit', $formulario));
        $this->assertDatabaseHas('formularios', [
            'id' => $formulario->id,
            'nome' => 'Avaliacao Operacional',
            'tipo' => FormularioTipo::Industria->value,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('perguntas', [
            'id' => $pergunta->id,
            'titulo' => 'Como esta a entrega?',
            'ordem' => 2,
            'obrigatoria' => true,
        ]);
    }

    public function test_rh_can_add_remove_and_restore_question(): void
    {
        [$rh, $formulario, $pergunta] = $this->makeFormulario();

        $this->actingAs($rh)->post(route('formularios.perguntas.store', $formulario), [
            'titulo' => 'Relacionamento com a equipe',
            'descricao' => 'Observacoes do gestor.',
            'tipo' => PerguntaTipo::TextoLongo->value,
            'obrigatoria' => '1',
        ])->assertRedirect(route('formularios.edit', $formulario));

        $this->assertDatabaseHas('perguntas', [
            'formulario_id' => $formulario->id,
            'titulo' => 'Relacionamento com a equipe',
            'ordem' => 2,
            'is_active' => true,
        ]);

        $this->actingAs($rh)->delete(route('formularios.perguntas.destroy', [$formulario, $pergunta]))
            ->assertRedirect(route('formularios.edit', $formulario));

        $this->assertDatabaseHas('perguntas', [
            'id' => $pergunta->id,
            'is_active' => false,
        ]);

        $this->actingAs($rh)->patch(route('formularios.perguntas.restore', [$formulario, $pergunta]))
            ->assertRedirect(route('formularios.edit', $formulario));

        $this->assertDatabaseHas('perguntas', [
            'id' => $pergunta->id,
            'is_active' => true,
        ]);
    }

    private function makeFormulario(): array
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $formulario = Formulario::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Avaliacao ADM',
            'tipo' => FormularioTipo::Administrativo,
            'is_active' => true,
        ]);
        $pergunta = Pergunta::create([
            'formulario_id' => $formulario->id,
            'titulo' => 'Pontualidade',
            'tipo' => PerguntaTipo::TextoLongo,
            'ordem' => 1,
            'obrigatoria' => true,
            'is_active' => true,
        ]);

        return [$rh, $formulario, $pergunta];
    }
}
