<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rh_can_view_usuarios(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        User::factory()->create([
            'empresa_id' => $empresa->id,
            'name' => 'Alerta RH',
            'email' => 'alerta@example.com',
            'role' => UserRole::Rh,
        ]);

        $this->actingAs($rh)
            ->get(route('usuarios.index'))
            ->assertOk()
            ->assertSee('Alerta RH')
            ->assertSee('Conclusões de avaliações');
    }

    public function test_rh_can_create_rh_user_for_alerts(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);

        $this->actingAs($rh)
            ->post(route('usuarios.store'), [
                'name' => 'Financeiro RH',
                'email' => 'financeiro-rh@example.com',
                'phone' => '11999990000',
                'role' => UserRole::Rh->value,
                'password' => 'secret123',
                'is_active' => '1',
            ])
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'empresa_id' => $empresa->id,
            'name' => 'Financeiro RH',
            'email' => 'financeiro-rh@example.com',
            'role' => UserRole::Rh->value,
            'is_active' => true,
        ]);
    }
}
