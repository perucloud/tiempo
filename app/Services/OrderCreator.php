<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoEstado;
use App\Support\ShoppingCart;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderCreator
{
    public function __construct(
        private readonly DeliveryPricingService $pricing,
    ) {}

    public function createFromCart(ShoppingCart $cart, array $customerData, ?float $latitud = null, ?float $longitud = null): Pedido
    {
        $summary = $cart->summary();

        if ($summary['items']->isEmpty()) {
            throw new RuntimeException('El carrito esta vacio.');
        }

        if (blank($summary['delivery_address'])) {
            throw new RuntimeException('La direccion de entrega es obligatoria.');
        }

        if ($latitud === null || $longitud === null) {
            throw new RuntimeException('Comparte tu ubicación para validar cobertura y calcular el delivery.');
        }

        $business = $summary['items']->first()['product']->negocioAfiliado;
        $quote = $this->pricing->calculate(
            $business,
            $latitud,
            $longitud,
            number_format((float) $summary['subtotal'], 2, '.', ''),
        );

        if (! $quote->available) {
            throw new RuntimeException($quote->unavailableReason ?? 'No hay cobertura para tu ubicación.');
        }

        $delivery = (float) $quote->finalDeliveryPrice;
        $total = (float) $summary['subtotal'] + $delivery;

        return DB::transaction(function () use ($cart, $summary, $customerData, $latitud, $longitud, $quote, $delivery, $total): Pedido {
            $client = Cliente::query()->updateOrCreate(
                ['telefono' => $customerData['telefono']],
                [
                    'nombres' => $customerData['nombres'],
                    'apellidos' => $customerData['apellidos'] ?? null,
                    'email' => $customerData['email'] ?? null,
                    'documento' => $customerData['documento'] ?? null,
                    'estado' => Cliente::ESTADO_ACTIVO,
                ],
            );

            $pedido = Pedido::query()->create([
                'codigo'              => Pedido::nextCode(),
                'cliente_id'          => $client->id,
                'negocio_afiliado_id' => $summary['business_id'],
                'estado'              => Pedido::ESTADO_PENDIENTE,
                'estado_pago'         => Pedido::PAGO_PENDIENTE,
                'direccion_entrega'   => $summary['delivery_address'],
                'latitud_cliente'     => $latitud,
                'longitud_cliente'    => $longitud,
                'geolocalizacion_at'  => ($latitud !== null && $longitud !== null) ? now() : null,
                'subtotal'            => $summary['subtotal'],
                'costo_delivery'      => $delivery,
                'total'               => $total,
                'notas'               => $customerData['notas'] ?? null,
                'zona_delivery_id'    => $quote->zoneId,
                'distance_km'         => $quote->distanceKm,
                'delivery_duration_minutes' => (int) round($quote->routeDurationMinutes),
                'delivery_pricing_snapshot' => $quote->pricingSnapshot(),
            ]);

            foreach ($summary['items'] as $item) {
                $pedido->detalles()->create([
                    'producto_id' => $item['product']->id,
                    'producto_nombre' => $item['product']->nombre,
                    'cantidad' => $item['quantity'],
                    'precio_unitario' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            PedidoEstado::query()->create([
                'pedido_id' => $pedido->id,
                'estado_anterior' => null,
                'estado_nuevo' => Pedido::ESTADO_PENDIENTE,
                'comentario' => 'Pedido creado desde la app.',
            ]);

            $cart->clear();

            session()->put('app_customer_phone', $client->telefono);
            session()->push('app_order_ids', $pedido->id);

            return $pedido->load(['cliente', 'negocioAfiliado', 'detalles', 'estados']);
        });
    }
}
