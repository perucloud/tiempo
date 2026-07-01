<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminClientsTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_clients_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        Cliente::factory()->create(['nombres' => 'Ana', 'apellidos' => 'Torres']);

        $this->actingAs($operator)
            ->get('/admin/clients')
            ->assertOk()
            ->assertSee('Clientes')
            ->assertSee('Ana Torres')
            ->assertSee('Nuevo cliente');
    }

    public function test_admin_can_create_client(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->post('/admin/clients', [
                'nombres' => 'Carlos',
                'apellidos' => 'Ramos',
                'telefono' => '999888777',
                'email' => 'carlos@tiempo.test',
                'documento' => '12345678',
                'estado' => Cliente::ESTADO_ACTIVO,
            ])
            ->assertRedirect('/admin/clients');

        $this->assertDatabaseHas('clientes', [
            'nombres' => 'Carlos',
            'apellidos' => 'Ramos',
            'telefono' => '999888777',
            'estado' => Cliente::ESTADO_ACTIVO,
        ]);
    }

    public function test_admin_can_update_client(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $client = Cliente::factory()->create(['telefono' => '900111222']);

        $this->actingAs($admin)
            ->put("/admin/clients/{$client->id}", [
                'nombres' => 'Maria',
                'apellidos' => 'Lopez',
                'telefono' => '900111333',
                'email' => 'maria@tiempo.test',
                'documento' => '87654321',
                'estado' => Cliente::ESTADO_INACTIVO,
            ])
            ->assertRedirect('/admin/clients');

        $this->assertDatabaseHas('clientes', [
            'id' => $client->id,
            'nombres' => 'Maria',
            'telefono' => '900111333',
            'estado' => Cliente::ESTADO_INACTIVO,
        ]);
    }

    public function test_clients_can_be_filtered_by_search_and_status(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        Cliente::factory()->create(['nombres' => 'Lucia', 'telefono' => '911111111', 'estado' => Cliente::ESTADO_ACTIVO]);
        Cliente::factory()->create(['nombres' => 'Pedro', 'telefono' => '922222222', 'estado' => Cliente::ESTADO_INACTIVO]);

        $this->actingAs($admin)
            ->get('/admin/clients?search=Lucia&estado=activo')
            ->assertOk()
            ->assertSee('Lucia')
            ->assertDontSee('Pedro');
    }

    public function test_admin_can_soft_delete_client(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $client = Cliente::factory()->create();

        $this->actingAs($admin)
            ->delete("/admin/clients/{$client->id}")
            ->assertRedirect('/admin/clients');

        $this->assertSoftDeleted('clientes', ['id' => $client->id]);
    }

    public function test_affiliated_business_cannot_manage_clients(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/clients')
            ->assertForbidden();
    }

    public function test_admin_cannot_create_duplicate_active_phone(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        Cliente::factory()->create(['telefono' => '955555555']);

        $this->actingAs($admin)
            ->from('/admin/clients/create')
            ->post('/admin/clients', [
                'nombres' => 'Cliente',
                'telefono' => '955555555',
                'estado' => Cliente::ESTADO_ACTIVO,
            ])
            ->assertRedirect('/admin/clients/create')
            ->assertSessionHasErrors('telefono');
    }

    private function makeUser(string $role): User
    {
        return User::query()->create([
            'name' => "Usuario {$role}",
            'email' => "{$role}@tiempo.test",
            'password' => Hash::make('secret-password'),
            'role' => $role,
            'status' => User::STATUS_ACTIVE,
        ]);
    }
}
