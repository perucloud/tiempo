<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegocioDeliveryConfig extends Model
{
    protected $table = 'negocio_delivery_configs';

    protected $fillable = [
        'negocio_afiliado_id',
        'permite_delivery',
        'distancia_maxima_km',
        'pedido_minimo',
        'delivery_gratis_desde',
        'precio_base_custom',
        'precio_por_km_custom',
    ];

    protected function casts(): array
    {
        return [
            'permite_delivery'      => 'boolean',
            'distancia_maxima_km'   => 'decimal:2',
            'pedido_minimo'         => 'decimal:2',
            'delivery_gratis_desde' => 'decimal:2',
            'precio_base_custom'    => 'decimal:2',
            'precio_por_km_custom'  => 'decimal:2',
        ];
    }

    public function negocioAfiliado(): BelongsTo
    {
        return $this->belongsTo(NegocioAfiliado::class);
    }
}
