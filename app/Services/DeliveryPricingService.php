<?php

namespace App\Services;

use App\Contracts\Geo\RoutingProviderInterface;
use App\DTOs\Delivery\DeliveryPricingResult;
use App\Models\NegocioAfiliado;
use App\Models\ZonaDelivery;

/**
 * Servicio central de tarificación de delivery.
 *
 * Responsabilidades:
 * - Validar que el negocio acepte delivery.
 * - Detectar la zona de cobertura del cliente mediante polígonos.
 * - Consultar OSRM para obtener la distancia vial real.
 * - Calcular el desglose completo de tarifa usando aritmética de strings (no float).
 * - Devolver un DeliveryPricingResult inmutable listo para display, API y snapshot de auditoría.
 *
 * Lo que NO hace este servicio:
 * - No guarda en base de datos (eso lo hace el flujo del pedido).
 * - No calcula precios de productos.
 * - No rastrea repartidores.
 */
class DeliveryPricingService
{
    public function __construct(
        private readonly RoutingProviderInterface $routing,
    ) {}

    /**
     * Calcula el costo de delivery entre un negocio y la ubicación del cliente.
     *
     * @param string $orderSubtotal Monto de los productos en formato "X.XX" (2 decimales)
     */
    public function calculate(
        NegocioAfiliado $negocio,
        float $clientLat,
        float $clientLng,
        string $orderSubtotal = '0.00',
    ): DeliveryPricingResult {
        /* 1. Verificar configuración del negocio */
        $config = $negocio->deliveryConfig;

        if ($config !== null && ! $config->permite_delivery) {
            return DeliveryPricingResult::unavailable(
                'Este negocio no realiza delivery actualmente.',
            );
        }

        /* 2. Verificar que el negocio tenga coordenadas */
        if (! $negocio->latitud || ! $negocio->longitud) {
            return DeliveryPricingResult::unavailable(
                'El negocio no tiene ubicación registrada.',
            );
        }

        /* 3. Identificar zona del cliente */
        $zone = $this->findZoneForPoint($clientLat, $clientLng);

        if ($zone === null) {
            return DeliveryPricingResult::unavailable(
                'Tu ubicación está fuera del área de cobertura disponible.',
            );
        }

        $minimumOrder = $config?->pedido_minimo ?? $zone->pedido_minimo;

        if ($minimumOrder !== null && (float) $orderSubtotal < (float) $minimumOrder) {
            return DeliveryPricingResult::unavailable(
                'El pedido mínimo para esta zona es S/ '.number_format((float) $minimumOrder, 2),
            );
        }

        /* 4. Consultar OSRM — distancia vial real */
        $route = $this->routing->route(
            (float) $negocio->latitud,
            (float) $negocio->longitud,
            $clientLat,
            $clientLng,
        );

        if (! $route->routeFound) {
            return DeliveryPricingResult::unavailable(
                'No se encontró ruta vial accesible para tu ubicación.',
            );
        }

        $distanceKm  = $route->distanceKilometers;
        $durationMin = $route->durationMinutes;

        /* 5. Validar distancia máxima (negocio > zona > sin límite) */
        $maxKm = $config?->distancia_maxima_km ?? $zone->distancia_maxima_km;

        if ($maxKm !== null && $distanceKm > (float) $maxKm) {
            return DeliveryPricingResult::unavailable(
                "Tu ubicación supera la distancia máxima de {$maxKm} km para esta zona.",
            );
        }

        /* 6. Precio base (negocio puede tener precio custom) */
        $basePrice = (string) ($config?->precio_base_custom ?? $zone->costo_delivery ?? '0.00');

        /* 7. Kilómetros extra y su costo */
        $kmIncluidos  = (float) ($zone->km_incluidos ?? '0');
        $pricePerKm   = (string) ($config?->precio_por_km_custom ?? $zone->precio_por_km_extra ?? '0.00');
        $extraKm      = max(0.0, $distanceKm - $kmIncluidos);
        $extraCost    = $this->money($extraKm * (float) $pricePerKm);

        /* 8. Recargo de zona */
        $surcharge = (string) ($zone->recargo ?? '0.00');

        /* Total parcial */
        $total = $this->money(
            (float) $basePrice + (float) $extraCost + (float) $surcharge,
        );

        /* 9. Delivery gratis */
        $freeFrom = $config?->delivery_gratis_desde ?? $zone->delivery_gratis_desde;
        $discount  = '0.00';

        if ($freeFrom !== null && (float) $orderSubtotal >= (float) $freeFrom) {
            $discount = $total;
            $total    = '0.00';
        }

        /* 10. Tiempo estimado */
        $prepMin  = (int) ($negocio->tiempo_preparacion ?? 20);
        $totalMin = $durationMin + $prepMin;

        return new DeliveryPricingResult(
            available:             true,
            unavailableReason:     null,
            zoneName:              $zone->nombre,
            zoneId:                $zone->id,
            distanceKm:            $distanceKm,
            routeDurationMinutes:  $durationMin,
            preparationMinutes:    $prepMin,
            estimatedTotalMinutes: $totalMin,
            basePrice:             $basePrice,
            extraKilometers:       round($extraKm, 3),
            extraKilometerCost:    $extraCost,
            zoneSurcharge:         $surcharge,
            discounts:             $discount,
            finalDeliveryPrice:    $total,
        );
    }

    /**
     * Encuentra la zona activa (con polígono) que contiene el punto dado.
     * En caso de superposición, gana la zona con menor valor de prioridad.
     */
    public function findZoneForPoint(float $lat, float $lng): ?ZonaDelivery
    {
        $zones = ZonaDelivery::query()
            ->where('activo', true)
            ->whereNotNull('polygon')
            ->orderBy('prioridad')
            ->get();

        foreach ($zones as $zone) {
            if ($zone->containsPoint($lat, $lng)) {
                return $zone;
            }
        }

        return null;
    }

    /**
     * Convierte un float a string con exactamente 2 decimales para persistencia.
     * Nunca almacenar el float directamente en la base de datos.
     */
    private function money(float $value): string
    {
        return number_format(round($value, 2), 2, '.', '');
    }
}
