<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Repartidor;
use App\Models\RepartidorUbicacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GeolocationTest extends TestCase
{
    use RefreshDatabase;

    /* -------------------------------------------------------
     | API: ubicación del cliente
     ------------------------------------------------------- */

    public function test_client_can_save_location_on_existing_order(): void
    {
        $pedido = Pedido::factory()->create(['codigo' => 'PED-GEO-TEST']);

        $this->postJson("/api/v1/pedidos/{$pedido->codigo}/ubicacion", [
            'latitud'  => -12.0464,
            'longitud' => -77.0428,
        ])->assertOk()
            ->assertJsonPath('data.codigo', $pedido->codigo);

        $this->assertDatabaseHas('pedidos', [
            'id'              => $pedido->id,
            'latitud_cliente' => -12.0464000,
            'longitud_cliente' => -77.0428000,
        ]);
    }

    public function test_client_location_returns_404_for_unknown_order(): void
    {
        $this->postJson('/api/v1/pedidos/NO-EXISTE/ubicacion', [
            'latitud'  => -12.0464,
            'longitud' => -77.0428,
        ])->assertStatus(404);
    }

    public function test_client_location_validates_coordinate_bounds(): void
    {
        $pedido = Pedido::factory()->create(['codigo' => 'PED-VAL-TEST']);

        $this->postJson("/api/v1/pedidos/{$pedido->codigo}/ubicacion", [
            'latitud'  => 999,
            'longitud' => -77.0428,
        ])->assertStatus(422);

        $this->postJson("/api/v1/pedidos/{$pedido->codigo}/ubicacion", [
            'latitud'  => -12.0464,
            'longitud' => 999,
        ])->assertStatus(422);
    }

    /* -------------------------------------------------------
     | API: posición del repartidor
     ------------------------------------------------------- */

    public function test_courier_can_update_location(): void
    {
        $courier = Repartidor::factory()->create(['estado' => Repartidor::ESTADO_DISPONIBLE]);

        $this->postJson('/api/v1/repartidores/ubicacion', [
            'repartidor_id' => $courier->id,
            'latitud'       => -12.0500,
            'longitud'      => -77.0400,
        ])->assertOk();

        $this->assertDatabaseHas('repartidores', [
            'id'             => $courier->id,
            'latitud_actual' => -12.0500000,
            'longitud_actual' => -77.0400000,
        ]);

        $this->assertDatabaseHas('repartidor_ubicaciones', [
            'repartidor_id' => $courier->id,
            'latitud'       => -12.0500000,
        ]);
    }

    public function test_inactive_courier_cannot_update_location(): void
    {
        $courier = Repartidor::factory()->create(['estado' => Repartidor::ESTADO_INACTIVO]);

        $this->postJson('/api/v1/repartidores/ubicacion', [
            'repartidor_id' => $courier->id,
            'latitud'       => -12.0500,
            'longitud'      => -77.0400,
        ])->assertStatus(403);
    }

    public function test_courier_location_validates_required_fields(): void
    {
        $this->postJson('/api/v1/repartidores/ubicacion', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['repartidor_id', 'latitud', 'longitud']);
    }

    public function test_courier_location_update_logs_to_history_table(): void
    {
        $courier = Repartidor::factory()->create(['estado' => Repartidor::ESTADO_OCUPADO]);

        $this->postJson('/api/v1/repartidores/ubicacion', [
            'repartidor_id' => $courier->id,
            'latitud'       => -12.1,
            'longitud'      => -77.1,
        ])->assertOk();

        $this->assertEquals(1, RepartidorUbicacion::query()->where('repartidor_id', $courier->id)->count());
    }

    /* -------------------------------------------------------
     | Admin: vista de tracking — acceso por rol
     ------------------------------------------------------- */

    public function test_operator_can_access_tracking_view(): void
    {
        $user = User::factory()->create([
            'role'   => User::ROLE_OPERADOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($user)
            ->get(route('admin.couriers.tracking'))
            ->assertOk()
            ->assertSee('Tracking en vivo');
    }

    public function test_tracking_view_blocked_for_guests(): void
    {
        $this->get(route('admin.couriers.tracking'))
            ->assertRedirect(route('admin.login'));
    }

    /* -------------------------------------------------------
     | Modelo: Pedido — helpers de geolocalización
     ------------------------------------------------------- */

    public function test_pedido_has_geolocalizacion_returns_true_when_coordinates_present(): void
    {
        $pedido = Pedido::factory()->create([
            'latitud_cliente'  => -12.05,
            'longitud_cliente' => -77.04,
        ]);

        $this->assertTrue($pedido->tieneGeolocalizacion());
    }

    public function test_pedido_has_geolocalizacion_returns_false_when_null(): void
    {
        $pedido = Pedido::factory()->create([
            'latitud_cliente'  => null,
            'longitud_cliente' => null,
        ]);

        $this->assertFalse($pedido->tieneGeolocalizacion());
    }

    /* -------------------------------------------------------
     | Modelo: Repartidor — GPS activo
     ------------------------------------------------------- */

    public function test_repartidor_gps_activo_returns_true_within_2_minutes(): void
    {
        $courier = Repartidor::factory()->create([
            'ubicacion_actualizada_at' => now()->subMinute(),
        ]);

        $this->assertTrue($courier->tieneGpsActivo());
    }

    public function test_repartidor_gps_activo_returns_false_after_timeout(): void
    {
        $courier = Repartidor::factory()->create([
            'ubicacion_actualizada_at' => now()->subMinutes(5),
        ]);

        $this->assertFalse($courier->tieneGpsActivo());
    }

    /* -------------------------------------------------------
     | Admin: endpoint JSON de ubicaciones activas
     ------------------------------------------------------- */

    public function test_admin_ubicaciones_endpoint_returns_active_couriers(): void
    {
        $user = User::factory()->create([
            'role'   => User::ROLE_OPERADOR,
            'status' => User::STATUS_ACTIVE,
        ]);

        Repartidor::factory()->create([
            'estado'                  => Repartidor::ESTADO_OCUPADO,
            'latitud_actual'          => -12.05,
            'longitud_actual'         => -77.04,
            'ubicacion_actualizada_at' => now()->subMinutes(1),
        ]);

        $this->actingAs($user)
            ->getJson(route('admin.couriers.ubicaciones'))
            ->assertOk()
            ->assertJsonPath('data.0.estado', 'ocupado');
    }
}
