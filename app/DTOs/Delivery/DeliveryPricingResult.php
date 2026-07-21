<?php

namespace App\DTOs\Delivery;

/**
 * DTO inmutable que representa el resultado completo del cálculo de tarifa de delivery.
 *
 * Los campos monetarios son strings de 2 decimales (ej: "5.50") para garantizar
 * precisión exacta, sin errores de punto flotante en persistencia o comparación.
 * Los campos geométricos (distanceKm, durationMinutes) son float ya que solo
 * se usan para display, no se persisten directamente.
 *
 * El método pricingSnapshot() genera el JSON listo para guardar en
 * pedidos.delivery_pricing_snapshot — inmutable por diseño.
 */
final readonly class DeliveryPricingResult
{
    public function __construct(
        public bool    $available,
        public ?string $unavailableReason,
        public ?string $zoneName,
        public ?int    $zoneId,
        public float   $distanceKm,
        public float   $routeDurationMinutes,
        public int     $preparationMinutes,
        public float   $estimatedTotalMinutes,
        public string  $basePrice,
        public float   $extraKilometers,
        public string  $extraKilometerCost,
        public string  $zoneSurcharge,
        public string  $discounts,
        public string  $finalDeliveryPrice,
    ) {}

    public static function unavailable(string $reason): self
    {
        return new self(
            available:             false,
            unavailableReason:     $reason,
            zoneName:              null,
            zoneId:                null,
            distanceKm:            0.0,
            routeDurationMinutes:  0.0,
            preparationMinutes:    0,
            estimatedTotalMinutes: 0.0,
            basePrice:             '0.00',
            extraKilometers:       0.0,
            extraKilometerCost:    '0.00',
            zoneSurcharge:         '0.00',
            discounts:             '0.00',
            finalDeliveryPrice:    '0.00',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'available'               => $this->available,
            'unavailable_reason'      => $this->unavailableReason,
            'zone'                    => $this->zoneName,
            'zone_id'                 => $this->zoneId,
            'distance_km'             => round($this->distanceKm, 3),
            'route_duration_minutes'  => round($this->routeDurationMinutes, 1),
            'preparation_minutes'     => $this->preparationMinutes,
            'estimated_total_minutes' => round($this->estimatedTotalMinutes, 1),
            'base_price'              => $this->basePrice,
            'extra_kilometers'        => round($this->extraKilometers, 3),
            'extra_kilometer_cost'    => $this->extraKilometerCost,
            'zone_surcharge'          => $this->zoneSurcharge,
            'discounts'               => $this->discounts,
            'final_delivery_price'    => $this->finalDeliveryPrice,
        ];
    }

    /**
     * Snapshot para persistir en pedidos.delivery_pricing_snapshot.
     * Una vez guardado, nunca se recalcula aunque cambien las tarifas.
     *
     * @return array<string, mixed>
     */
    public function pricingSnapshot(): array
    {
        return array_merge($this->toArray(), [
            'calculated_at' => now()->toISOString(),
        ]);
    }
}
