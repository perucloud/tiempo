<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoAsignacion extends Model
{
    public $timestamps = false;

    protected $table = 'pedido_asignaciones';

    public const TYPE_MANUAL    = 'manual';
    public const TYPE_AUTOMATICO = 'automatico';

    public const STATUS_ACTIVO     = 'activo';
    public const STATUS_CANCELADO  = 'cancelado';
    public const STATUS_COMPLETADO = 'completado';

    protected $fillable = [
        'pedido_id',
        'repartidor_id',
        'assigned_by',
        'assignment_type',
        'status',
        'distance_to_business_km',
        'estimated_time_to_business_min',
        'route_to_business',
        'distance_to_customer_km',
        'estimated_time_to_customer_min',
        'route_to_customer',
        'notes',
        'assigned_at',
        'canceled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'route_to_business'               => 'array',
            'route_to_customer'               => 'array',
            'distance_to_business_km'         => 'decimal:3',
            'distance_to_customer_km'         => 'decimal:3',
            'estimated_time_to_business_min'  => 'integer',
            'estimated_time_to_customer_min'  => 'integer',
            'assigned_at'                     => 'datetime',
            'canceled_at'                     => 'datetime',
            'completed_at'                    => 'datetime',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(Repartidor::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVO;
    }
}
