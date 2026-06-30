<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_PAGO_EN_REVISION = 'pago_en_revision';

    public const ESTADO_CONFIRMADO = 'confirmado';

    public const ESTADO_PREPARANDO = 'preparando';

    public const ESTADO_LISTO = 'listo';

    public const ESTADO_ASIGNADO = 'asignado';

    public const ESTADO_EN_CAMINO = 'en_camino';

    public const ESTADO_ENTREGADO = 'entregado';

    public const ESTADO_CANCELADO = 'cancelado';

    protected $fillable = [
        'codigo',
        'cliente_id',
        'negocio_afiliado_id',
        'repartidor_id',
        'operador_id',
        'estado',
        'estado_pago',
        'direccion_entrega',
        'referencia_entrega',
        'subtotal',
        'costo_delivery',
        'total',
        'notas',
        'confirmado_at',
        'entregado_at',
        'cancelado_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'costo_delivery' => 'decimal:2',
            'total' => 'decimal:2',
            'confirmado_at' => 'datetime',
            'entregado_at' => 'datetime',
            'cancelado_at' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function negocioAfiliado(): BelongsTo
    {
        return $this->belongsTo(NegocioAfiliado::class);
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(Repartidor::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function estados(): HasMany
    {
        return $this->hasMany(PedidoEstado::class);
    }
}
