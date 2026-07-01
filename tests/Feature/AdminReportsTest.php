<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_global_reports(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $business = NegocioAfiliado::factory()->create(['nombre_comercial' => 'Polleria Central']);
        $courier = Repartidor::factory()->create(['nombres' => 'Luis', 'apellidos' => 'Rojas']);
        $order = $this->makeOrder([
            'negocio_afiliado_id' => $business->id,
            'repartidor_id' => $courier->id,
            'estado' => Pedido::ESTADO_ENTREGADO,
            'total' => 45,
            'created_at' => now(),
        ]);
        $payment = Pago::query()->create([
            'pedido_id' => $order->id,
            'metodo' => Pago::METODO_YAPE,
            'monto' => 45,
            'estado' => Pago::ESTADO_APROBADO,
        ]);
        $payment->forceFill(['created_at' => now()])->save();

        $this->actingAs($admin)
            ->get('/admin/reports')
            ->assertOk()
            ->assertSee('Reportes administrativos')
            ->assertSee('S/ 45.00')
            ->assertSee('Polleria Central')
            ->assertSee('Luis Rojas')
            ->assertSee('Yape');
    }

    public function test_reports_can_be_filtered_by_date_range(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $inside = $this->makeOrder([
            'codigo' => 'PED-DENTRO',
            'estado' => Pedido::ESTADO_ENTREGADO,
            'total' => 60,
            'created_at' => '2026-06-10 10:00:00',
        ]);
        $outside = $this->makeOrder([
            'codigo' => 'PED-FUERA',
            'estado' => Pedido::ESTADO_ENTREGADO,
            'total' => 90,
            'created_at' => '2026-05-10 10:00:00',
        ]);
        $insidePayment = Pago::query()->create([
            'pedido_id' => $inside->id,
            'metodo' => Pago::METODO_PLIN,
            'monto' => 60,
            'estado' => Pago::ESTADO_APROBADO,
        ]);
        $insidePayment->forceFill(['created_at' => '2026-06-10 10:00:00'])->save();

        $outsidePayment = Pago::query()->create([
            'pedido_id' => $outside->id,
            'metodo' => Pago::METODO_YAPE,
            'monto' => 90,
            'estado' => Pago::ESTADO_APROBADO,
        ]);
        $outsidePayment->forceFill(['created_at' => '2026-05-10 10:00:00'])->save();

        $this->actingAs($admin)
            ->get('/admin/reports?date_from=2026-06-01&date_to=2026-06-30')
            ->assertOk()
            ->assertSee('S/ 60.00')
            ->assertDontSee('S/ 90.00');
    }

    public function test_operator_cannot_view_global_reports(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);

        $this->actingAs($operator)
            ->get('/admin/reports')
            ->assertForbidden();
    }

    public function test_affiliated_business_cannot_view_global_reports(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/reports')
            ->assertForbidden();
    }

    private function makeOrder(array $attributes = []): Pedido
    {
        $createdAt = $attributes['created_at'] ?? null;
        unset($attributes['created_at']);

        $order = Pedido::query()->create(array_merge([
            'codigo' => Pedido::nextCode(),
            'cliente_id' => Cliente::factory()->create()->id,
            'negocio_afiliado_id' => NegocioAfiliado::factory()->create()->id,
            'estado' => Pedido::ESTADO_PENDIENTE,
            'estado_pago' => Pedido::PAGO_PENDIENTE,
            'direccion_entrega' => 'Av. Principal 123',
            'subtotal' => 30,
            'costo_delivery' => 5,
            'total' => 35,
        ], $attributes));

        if ($createdAt) {
            $order->forceFill(['created_at' => $createdAt])->save();
        }

        return $order;
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
