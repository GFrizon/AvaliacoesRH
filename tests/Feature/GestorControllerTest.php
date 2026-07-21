<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GestorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_rh_can_reactivate_inactive_gestor_when_creating_with_same_email(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $gestor = User::factory()->create([
            'empresa_id' => $empresa->id,
            'name' => 'Gestor Antigo',
            'email' => 'ti@bakof.com.br',
            'role' => UserRole::Gestor,
            'is_active' => false,
        ]);

        $this->actingAs($rh)
            ->post(route('gestores.store'), [
                'name' => 'TI',
                'email' => 'ti@bakof.com.br',
                'phone' => '(55) 3744-9999',
                'password' => 'secret123',
                'is_active' => '1',
            ])
            ->assertRedirect(route('gestores.index'))
            ->assertSessionHas('status', 'Gestor cadastrado com sucesso.');

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', [
            'id' => $gestor->id,
            'empresa_id' => $empresa->id,
            'name' => 'TI',
            'email' => 'ti@bakof.com.br',
            'role' => UserRole::Gestor->value,
            'phone' => '(55) 3744-9999',
            'is_active' => true,
        ]);
    }

    public function test_rh_can_convert_same_company_user_to_gestor_when_creating_with_same_email(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        $usuario = User::factory()->create([
            'empresa_id' => $empresa->id,
            'name' => 'Usuario Antigo',
            'email' => 'ti@bakof.com.br',
            'role' => UserRole::Rh,
            'is_active' => true,
        ]);

        $this->actingAs($rh)
            ->post(route('gestores.store'), [
                'name' => 'TI',
                'email' => 'ti@bakof.com.br',
                'phone' => '(55) 3744-9999',
                'password' => 'secret123',
                'is_active' => '1',
            ])
            ->assertRedirect(route('gestores.index'))
            ->assertSessionHas('status', 'Gestor cadastrado com sucesso.');

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'empresa_id' => $empresa->id,
            'name' => 'TI',
            'email' => 'ti@bakof.com.br',
            'role' => UserRole::Gestor->value,
            'phone' => '(55) 3744-9999',
            'is_active' => true,
        ]);
    }

    public function test_rh_sees_friendly_error_when_email_belongs_to_another_company(): void
    {
        $empresa = Empresa::create(['nome' => 'Empresa Demo']);
        $outraEmpresa = Empresa::create(['nome' => 'Outra Empresa']);
        $rh = User::factory()->create([
            'empresa_id' => $empresa->id,
            'role' => UserRole::Rh,
        ]);
        User::factory()->create([
            'empresa_id' => $outraEmpresa->id,
            'email' => 'ti@bakof.com.br',
            'role' => UserRole::Rh,
            'is_active' => true,
        ]);

        $this->actingAs($rh)
            ->from(route('gestores.create'))
            ->post(route('gestores.store'), [
                'name' => 'TI',
                'email' => 'ti@bakof.com.br',
                'password' => 'secret123',
                'is_active' => '1',
            ])
            ->assertRedirect(route('gestores.create'))
            ->assertSessionHasErrors(['email' => 'Este e-mail ja esta em uso por outro usuario.']);
    }
}
