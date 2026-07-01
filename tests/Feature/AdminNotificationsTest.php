<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Notificacion;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_review_creates_client_and_internal_notifications(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $payment = $this->makePayment();

        $this->actingAs($operator)
            ->put("/admin/payments/{$payment->id}", [
                'estado' => Pago::ESTADO_APROBADO,
                'observacion' => 'Pago validado.',
            ])
            ->assertRedirect("/admin/payments/{$payment->id}/edit");

        $this->assertDatabaseHas('notificaciones', [
            'tipo' => Notificacion::TIPO_PAGO_APROBADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_CLIENTE,
            'cliente_id' => $payment->pedido->cliente_id,
            'pedido_id' => $payment->pedido_id,
            'pago_id' => $payment->id,
            'titulo' => 'Pago aprobado',
        ]);
        $this->assertDatabaseHas('notificaciones', [
            'tipo' => Notificacion::TIPO_PAGO_APROBADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_ADMIN,
            'pedido_id' => $payment->pedido_id,
        ]);
    }

    public function test_order_status_update_creates_client_notification(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $order = $this->makeOrder();

        $this->actingAs($operator)
            ->put("/admin/orders/{$order->id}", [
                'estado' => Pedido::ESTADO_PREPARANDO,
                'comentario' => 'Negocio preparando pedido.',
            ])
            ->assertRedirect("/admin/orders/{$order->id}/edit");

        $this->assertDatabaseHas('notificaciones', [
            'tipo' => Notificacion::TIPO_PEDIDO_ESTADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_CLIENTE,
            'cliente_id' => $order->cliente_id,
            'pedido_id' => $order->id,
            'titulo' => 'Estado del pedido actualizado',
        ]);
    }

    public function test_courier_assignment_creates_courier_notification(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $order = $this->makeOrder(['estado' => Pedido::ESTADO_LISTO]);
        $courier = Repartidor::factory()->create(['estado' => Repartidor::ESTADO_DISPONIBLE]);

        $this->actingAs($operator)
            ->patch("/admin/orders/{$order->id}/courier", [
                'repartidor_id' => $courier->id,
                'comentario' => 'Asignado para entrega.',
            ])
            ->assertRedirect("/admin/orders/{$order->id}/edit");

        $this->assertDatabaseHas('notificaciones', [
            'tipo' => Notificacion::TIPO_REPARTIDOR_ASIGNADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_REPARTIDOR,
            'repartidor_id' => $courier->id,
            'pedido_id' => $order->id,
            'titulo' => 'Nuevo pedido asignado',
        ]);
    }

    public function test_operator_can_view_notifications_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $order = $this->makeOrder();
        Notificacion::query()->create([
            'tipo' => Notificacion::TIPO_PEDIDO_ESTADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_CLIENTE,
            'cliente_id' => $order->cliente_id,
            'pedido_id' => $order->id,
            'titulo' => 'Estado del pedido actualizado',
            'mensaje' => 'Tu pedido cambio de estado.',
        ]);

        $this->actingAs($operator)
            ->get('/admin/notifications')
            ->assertOk()
            ->assertSee('Notificaciones')
            ->assertSee('Estado del pedido actualizado')
            ->assertSee($order->codigo);
    }

    public function test_affiliated_business_cannot_view_notifications(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/notifications')
            ->assertForbidden();
    }

    private function makePayment(array $attributes = []): Pago
    {
        $order = $this->makeOrder([
            'estado' => Pedido::ESTADO_PAGO_EN_REVISION,
            'estado_pago' => Pedido::PAGO_EN_REVISION,
        ]);

        return Pago::query()->create(array_merge([
            'pedido_id' => $order->id,
            'metodo' => Pago::METODO_YAPE,
            'monto' => $order->total,
            'estado' => Pago::ESTADO_PENDIENTE,
            'codigo_operacion' => 'OP123',
        ], $attributes));
    }

    private function makeOrder(array $attributes = []): Pedido
    {
        return Pedido::query()->create(array_merge([
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
