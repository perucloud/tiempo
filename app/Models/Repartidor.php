<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


use Illuminate\Database\Eloquent\SoftDeletes;

class Repartidor extends Model
{
    use HasFactory, SoftDeletes;

    /* ── Estado de disponibilidad (campo legacy) ── */
    public const ESTADO_DISPONIBLE = 'disponible';
    public const ESTADO_OCUPADO    = 'ocupado';
    public const ESTADO_INACTIVO   = 'inactivo';

    public const ESTADOS = [
        self::ESTADO_DISPONIBLE,
        self::ESTADO_OCUPADO,
        self::ESTADO_INACTIVO,
    ];

    /* ── Estado operativo granular (campo estado_operativo) ── */
    public const OP_OFFLINE            = 'offline';
    public const OP_AVAILABLE          = 'available';
    public const OP_ASSIGNED           = 'assigned';
    public const OP_GOING_TO_BUSINESS  = 'going_to_business';
    public const OP_AT_BUSINESS        = 'at_business';
    public const OP_PICKED_UP          = 'picked_up';
    public const OP_GOING_TO_CUSTOMER  = 'going_to_customer';
    public const OP_DELIVERED          = 'delivered';

    public const ESTADOS_OPERATIVOS = [
        self::OP_OFFLINE,
        self::OP_AVAILABLE,
        self::OP_ASSIGNED,
        self::OP_GOING_TO_BUSINESS,
        self::OP_AT_BUSINESS,
        self::OP_PICKED_UP,
        self::OP_GOING_TO_CUSTOMER,
        self::OP_DELIVERED,
    ];

    /* Transiciones permitidas: estado_actual → [estados_destino] */
    public const TRANSICIONES_OPERATIVAS = [
        self::OP_OFFLINE            => [],              // solo el sistema puede activarlo (iniciar turno)
        self::OP_AVAILABLE          => [],              // admin asigna
        self::OP_ASSIGNED           => [self::OP_GOING_TO_BUSINESS],
        self::OP_GOING_TO_BUSINESS  => [self::OP_AT_BUSINESS],
        self::OP_AT_BUSINESS        => [self::OP_PICKED_UP],
        self::OP_PICKED_UP          => [self::OP_GOING_TO_CUSTOMER],
        self::OP_GOING_TO_CUSTOMER  => [self::OP_DELIVERED],
        self::OP_DELIVERED          => [self::OP_AVAILABLE],
    ];

    protected $table = 'repartidores';

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'telefono',
        'documento',
        'vehiculo_tipo',
        'vehiculo_placa',
        'estado',
        'estado_operativo',
        'latitud_actual',
        'longitud_actual',
        'ubicacion_actualizada_at',
    ];

    protected function casts(): array
    {
        return [
            'latitud_actual'          => 'decimal:7',
            'longitud_actual'         => 'decimal:7',
            'ubicacion_actualizada_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(PedidoAsignacion::class);
    }

    public function asignacionActiva(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PedidoAsignacion::class)
            ->where('status', PedidoAsignacion::STATUS_ACTIVO)
            ->latest('assigned_at');
    }

    public function ubicaciones(): HasMany
    {
        return $this->hasMany(RepartidorUbicacion::class);
    }

    public function puedeTransicionar(string $nuevoEstado): bool
    {
        $actual = $this->estado_operativo ?? self::OP_OFFLINE;

        return in_array($nuevoEstado, self::TRANSICIONES_OPERATIVAS[$actual] ?? [], true);
    }

    public static function estadoOperativoLabel(string $estado): string
    {
        return match ($estado) {
            self::OP_OFFLINE            => 'Desconectado',
            self::OP_AVAILABLE          => 'Disponible',
            self::OP_ASSIGNED           => 'Pedido asignado',
            self::OP_GOING_TO_BUSINESS  => 'Yendo al negocio',
            self::OP_AT_BUSINESS        => 'En el negocio',
            self::OP_PICKED_UP          => 'Pedido recogido',
            self::OP_GOING_TO_CUSTOMER  => 'En camino al cliente',
            self::OP_DELIVERED          => 'Entregado',
            default                     => $estado,
        };
    }

    public function tieneGpsActivo(): bool
    {
        if ($this->ubicacion_actualizada_at === null) {
            return false;
        }

        return $this->ubicacion_actualizada_at->diffInMinutes(now()) <= 2;
    }

    public static function estadoOptions(): array
    {
        return [
            self::ESTADO_DISPONIBLE => 'Disponible',
            self::ESTADO_OCUPADO => 'Ocupado',
            self::ESTADO_INACTIVO => 'Inactivo',
        ];
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
