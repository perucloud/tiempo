<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 120);
        $delivery = fake()->randomFloat(2, 3, 12);

        return [
            'codigo' => 'PED-'.fake()->unique()->numerify('######'),
            'cliente_id' => Cliente::factory(),
            'negocio_afiliado_id' => NegocioAfiliado::factory(),
            'estado' => Pedido::ESTADO_PENDIENTE,
            'estado_pago' => 'pendiente',
            'direccion_entrega' => fake()->streetAddress(),
            'subtotal' => $subtotal,
            'costo_delivery' => $delivery,
            'total' => $subtotal + $delivery,
        ];
    }
}
