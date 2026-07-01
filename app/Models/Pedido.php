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

    public const PAGO_PENDIENTE = 'pendiente';

    public const PAGO_EN_REVISION = 'en_revision';

    public const PAGO_APROBADO = 'aprobado';

    public const PAGO_RECHAZADO = 'rechazado';

    public const ESTADOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_PAGO_EN_REVISION,
        self::ESTADO_CONFIRMADO,
        self::ESTADO_PREPARANDO,
        self::ESTADO_LISTO,
        self::ESTADO_ASIGNADO,
        self::ESTADO_EN_CAMINO,
        self::ESTADO_ENTREGADO,
        self::ESTADO_CANCELADO,
    ];

    public const ESTADOS_CLIENTE = [
        self::ESTADO_PENDIENTE => 'Pendiente de pago',
        self::ESTADO_PAGO_EN_REVISION => 'Pago en revision',
        self::ESTADO_CONFIRMADO => 'Pedido aprobado',
        self::ESTADO_PREPARANDO => 'En preparacion',
        self::ESTADO_LISTO => 'Listo para recojo',
        self::ESTADO_ASIGNADO => 'Repartidor asignado',
        self::ESTADO_EN_CAMINO => 'En camino',
        self::ESTADO_ENTREGADO => 'Entregado',
        self::ESTADO_CANCELADO => 'Cancelado',
    ];

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

    public static function estadoOptions(): array
    {
        return self::ESTADOS_CLIENTE;
    }

    public static function nextCode(): string
    {
        return 'PED-'.now()->format('Ymd').'-'.str_pad((string) (self::withTrashed()->count() + 1), 5, '0', STR_PAD_LEFT);
    }

    public function estadoLabel(): string
    {
        return self::ESTADOS_CLIENTE[$this->estado] ?? ucfirst($this->estado);
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
