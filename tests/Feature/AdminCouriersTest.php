<?php

namespace Tests\Feature;

use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCouriersTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_couriers_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        Repartidor::factory()->create(['nombres' => 'Luis', 'apellidos' => 'Rojas']);

        $this->actingAs($operator)
            ->get('/admin/couriers')
            ->assertOk()
            ->assertSee('Repartidores')
            ->assertSee('Luis Rojas')
            ->assertSee('Nuevo repartidor');
    }

    public function test_admin_can_create_courier(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->post('/admin/couriers', [
                'nombres' => 'Marco',
                'apellidos' => 'Vega',
                'telefono' => '988777666',
                'documento' => '12345678',
                'vehiculo_tipo' => 'moto',
                'vehiculo_placa' => 'ABC-123',
                'estado' => Repartidor::ESTADO_DISPONIBLE,
            ])
            ->assertRedirect('/admin/couriers');

        $this->assertDatabaseHas('repartidores', [
            'nombres' => 'Marco',
            'telefono' => '988777666',
            'estado' => Repartidor::ESTADO_DISPONIBLE,
        ]);
    }

    public function test_admin_can_update_courier(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $courier = Repartidor::factory()->create(['telefono' => '900111222']);

        $this->actingAs($admin)
            ->put("/admin/couriers/{$courier->id}", [
                'nombres' => 'Daniel',
                'apellidos' => 'Soto',
                'telefono' => '900111333',
                'documento' => '87654321',
                'vehiculo_tipo' => 'bicicleta',
                'vehiculo_placa' => 'B-22',
                'estado' => Repartidor::ESTADO_INACTIVO,
            ])
            ->assertRedirect('/admin/couriers');

        $this->assertDatabaseHas('repartidores', [
            'id' => $courier->id,
            'nombres' => 'Daniel',
            'telefono' => '900111333',
            'estado' => Repartidor::ESTADO_INACTIVO,
        ]);
    }

    public function test_couriers_can_be_filtered_by_search_and_status(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        Repartidor::factory()->create(['nombres' => 'Raul', 'telefono' => '911111111', 'estado' => Repartidor::ESTADO_DISPONIBLE]);
        Repartidor::factory()->create(['nombres' => 'Pedro', 'telefono' => '922222222', 'estado' => Repartidor::ESTADO_INACTIVO]);

        $this->actingAs($admin)
            ->get('/admin/couriers?search=Raul&estado=disponible')
            ->assertOk()
            ->assertSee('Raul')
            ->assertDontSee('Pedro');
    }

    public function test_admin_can_soft_delete_courier(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $courier = Repartidor::factory()->create();

        $this->actingAs($admin)
            ->delete("/admin/couriers/{$courier->id}")
            ->assertRedirect('/admin/couriers');

        $this->assertSoftDeleted('repartidores', ['id' => $courier->id]);
    }

    public function test_affiliated_business_cannot_manage_couriers(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/couriers')
            ->assertForbidden();
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
