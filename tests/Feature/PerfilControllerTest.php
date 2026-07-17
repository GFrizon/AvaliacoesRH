<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PerfilControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $user = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
            'name' => 'Nome Antigo',
        ]);

        $this->actingAs($user)
            ->put(route('perfil.update'), [
                'name' => 'Nome Novo',
                'phone' => '54999990000',
            ])
            ->assertSessionHas('status', 'Perfil atualizado com sucesso.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Novo',
            'phone' => '54999990000',
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $user = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
            'password' => Hash::make('Senha123'),
        ]);

        $this->actingAs($user)
            ->put(route('perfil.password'), [
                'current_password' => 'Senha123',
                'password' => 'NovaSenha123',
                'password_confirmation' => 'NovaSenha123',
            ])
            ->assertSessionHas('status', 'Senha alterada com sucesso.');

        $this->assertTrue(Hash::check('NovaSenha123', $user->refresh()->password));
    }
}
