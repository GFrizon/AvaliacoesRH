<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Empresa;
use App\Models\UnidadeNegocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnidadeNegocioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rh_can_create_unidade_negocio(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);

        $this->actingAs($rh)
            ->post(route('unidades-negocio.store'), [
                'nome' => 'Bakof Tec',
                'descricao' => 'Unidade de tecnologia',
                'is_active' => '1',
            ])
            ->assertRedirect(route('unidades-negocio.index'));

        $this->assertDatabaseHas('unidades_negocio', [
            'empresa_id' => $empresa->id,
            'nome' => 'Bakof Tec',
            'descricao' => 'Unidade de tecnologia',
            'is_active' => true,
        ]);
    }

    public function test_rh_can_deactivate_unidade_negocio(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $unidade = UnidadeNegocio::create([
            'empresa_id' => $empresa->id,
            'nome' => 'Bakof Matriz',
        ]);

        $this->actingAs($rh)
            ->delete(route('unidades-negocio.destroy', $unidade))
            ->assertRedirect(route('unidades-negocio.index'));

        $this->assertFalse($unidade->fresh()->is_active);
    }
}
