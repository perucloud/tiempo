<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_payments_index(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $payment = $this->makePayment();

        $this->actingAs($operator)
            ->get('/admin/payments')
            ->assertOk()
            ->assertSee('Pagos Yape/Plin')
            ->assertSee($payment->pedido->codigo);
    }

    public function test_operator_can_approve_payment_and_confirm_order(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $payment = $this->makePayment();

        $this->actingAs($operator)
            ->put("/admin/payments/{$payment->id}", [
                'estado' => Pago::ESTADO_APROBADO,
                'observacion' => 'Pago validado.',
            ])
            ->assertRedirect("/admin/payments/{$payment->id}/edit");

        $this->assertDatabaseHas('pagos', [
            'id' => $payment->id,
            'estado' => Pago::ESTADO_APROBADO,
            'verificado_por' => $operator->id,
            'observacion' => 'Pago validado.',
        ]);
        $this->assertDatabaseHas('pedidos', [
            'id' => $payment->pedido_id,
            'estado' => Pedido::ESTADO_CONFIRMADO,
            'estado_pago' => Pedido::PAGO_APROBADO,
        ]);
        $this->assertDatabaseHas('pedido_estados', [
            'pedido_id' => $payment->pedido_id,
            'estado_nuevo' => Pedido::ESTADO_CONFIRMADO,
            'comentario' => 'Pago validado.',
        ]);
    }

    public function test_operator_can_reject_payment_and_return_order_to_pending(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);
        $payment = $this->makePayment();

        $this->actingAs($operator)
            ->put("/admin/payments/{$payment->id}", [
                'estado' => Pago::ESTADO_RECHAZADO,
                'observacion' => 'Voucher no coincide.',
            ])
            ->assertRedirect("/admin/payments/{$payment->id}/edit");

        $this->assertDatabaseHas('pagos', [
            'id' => $payment->id,
            'estado' => Pago::ESTADO_RECHAZADO,
        ]);
        $this->assertDatabaseHas('pedidos', [
            'id' => $payment->pedido_id,
            'estado' => Pedido::ESTADO_PENDIENTE,
            'estado_pago' => Pedido::PAGO_RECHAZADO,
        ]);
    }

    public function test_payments_can_be_filtered_by_method_and_status(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $yape = $this->makePayment(['metodo' => Pago::METODO_YAPE, 'estado' => Pago::ESTADO_PENDIENTE]);
        $plin = $this->makePayment(['metodo' => Pago::METODO_PLIN, 'estado' => Pago::ESTADO_RECHAZADO]);

        $this->actingAs($admin)
            ->get('/admin/payments?metodo=yape&estado=pendiente')
            ->assertOk()
            ->assertSee($yape->pedido->codigo)
            ->assertDontSee($plin->pedido->codigo);
    }

    public function test_affiliated_business_cannot_manage_global_payments(): void
    {
        $affiliate = $this->makeUser(User::ROLE_NEGOCIO_AFILIADO);

        $this->actingAs($affiliate)
            ->get('/admin/payments')
            ->assertForbidden();
    }

    private function makePayment(array $attributes = []): Pago
    {
        $order = Pedido::query()->create([
            'codigo' => Pedido::nextCode(),
            'cliente_id' => Cliente::factory()->create()->id,
            'negocio_afiliado_id' => NegocioAfiliado::factory()->create()->id,
            'estado' => Pedido::ESTADO_PAGO_EN_REVISION,
            'estado_pago' => Pedido::PAGO_EN_REVISION,
            'direccion_entrega' => 'Av. Principal 123',
            'subtotal' => 30,
            'costo_delivery' => 5,
            'total' => 35,
        ]);

        return Pago::query()->create(array_merge([
            'pedido_id' => $order->id,
            'metodo' => Pago::METODO_YAPE,
            'monto' => $order->total,
            'estado' => Pago::ESTADO_PENDIENTE,
            'codigo_operacion' => 'OP123',
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
