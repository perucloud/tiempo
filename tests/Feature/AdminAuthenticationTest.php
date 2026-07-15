<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_area_redirects_guests_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_superadmin_can_login_to_admin_dashboard(): void
    {
        User::query()->create([
            'name' => 'SuperAdmin TIEMPO',
            'email' => 'admin@tiempo.test',
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_SUPERADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@tiempo.test',
            'password' => 'secret-password',
        ])->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Pedidos')
            ->assertSee('Pagos')
            ->assertSee('Repartidores')
            ->assertSee('Negocios afiliados')
            ->assertSee('Operacion rapida');
    }

    public function test_admin_dashboard_uses_real_operational_metrics(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin TIEMPO',
            'email' => 'admin-metricas@tiempo.test',
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);
        $cliente = Cliente::factory()->create(['nombres' => 'Ana', 'apellidos' => 'Torres']);
        Repartidor::factory()->create(['estado' => Repartidor::ESTADO_DISPONIBLE]);
        $pedido = Pedido::factory()->for($cliente)->create([
            'codigo' => 'PED-DASHBOARD',
            'estado' => Pedido::ESTADO_PENDIENTE,
            'total' => 42,
        ]);
        Pedido::factory()->create([
            'estado' => Pedido::ESTADO_ENTREGADO,
            'total' => 60,
        ]);
        Pago::query()->create([
            'pedido_id' => $pedido->id,
            'metodo' => Pago::METODO_YAPE,
            'monto' => 42,
            'estado' => Pago::ESTADO_PENDIENTE,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('PED-DASHBOARD')
            ->assertSee('Ana Torres')
            ->assertSee('S/ 60.00');
    }

    public function test_client_role_cannot_access_admin_dashboard(): void
    {
        $client = User::query()->create([
            'name' => 'Cliente TIEMPO',
            'email' => 'cliente@tiempo.test',
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_CLIENTE,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($client)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::query()->create([
            'name' => 'Operador TIEMPO',
            'email' => 'operador@tiempo.test',
            'password' => Hash::make('secret-password'),
            'role' => User::ROLE_OPERADOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }
}
