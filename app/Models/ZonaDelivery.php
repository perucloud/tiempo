<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZonaDelivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zonas_delivery';

    protected $fillable = [
        'nombre',
        'descripcion_cobertura',
        'polygon',
        'costo_delivery',
        'km_incluidos',
        'precio_por_km_extra',
        'delivery_gratis_desde',
        'recargo',
        'distancia_maxima_km',
        'tiempo_estimado_min',
        'tiempo_estimado_max',
        'pedido_minimo',
        'prioridad',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'polygon'               => 'array',
            'costo_delivery'        => 'decimal:2',
            'km_incluidos'          => 'decimal:2',
            'precio_por_km_extra'   => 'decimal:2',
            'delivery_gratis_desde' => 'decimal:2',
            'recargo'               => 'decimal:2',
            'distancia_maxima_km'   => 'decimal:2',
            'pedido_minimo'         => 'decimal:2',
            'prioridad'             => 'integer',
            'tiempo_estimado_min'   => 'integer',
            'tiempo_estimado_max'   => 'integer',
            'activo'                => 'boolean',
        ];
    }

    /**
     * Determina si el punto (lat, lng) se encuentra dentro del polígono de la zona.
     * Usa el algoritmo de ray casting. No requiere extensiones espaciales de MySQL.
     *
     * El polígono se almacena en orden GeoJSON: [[lng, lat], [lng, lat], ...]
     */
    public function containsPoint(float $lat, float $lng): bool
    {
        $polygon = $this->polygon;

        if (empty($polygon) || count($polygon) < 3) {
            return false;
        }

        return $this->rayCasting($lat, $lng, $polygon);
    }

    /**
     * @param array<int, array{0: float, 1: float}> $polygon [[lng, lat], ...]
     */
    private function rayCasting(float $lat, float $lng, array $polygon): bool
    {
        $n      = count($polygon);
        $inside = false;
        $j      = $n - 1;

        for ($i = 0; $i < $n; $i++) {
            $xi = $polygon[$i][0]; // lng (eje X)
            $yi = $polygon[$i][1]; // lat (eje Y)
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            if ((($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi)) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }

    /**
     * Devuelve true si la zona tiene un polígono válido dibujado.
     */
    public function tienePoligono(): bool
    {
        return is_array($this->polygon) && count($this->polygon) >= 3;
    }
}
