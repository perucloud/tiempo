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

    public const ESTADO_DISPONIBLE = 'disponible';

    public const ESTADO_OCUPADO = 'ocupado';

    public const ESTADO_INACTIVO = 'inactivo';

    public const ESTADOS = [
        self::ESTADO_DISPONIBLE,
        self::ESTADO_OCUPADO,
        self::ESTADO_INACTIVO,
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

    public function ubicaciones(): HasMany
    {
        return $this->hasMany(RepartidorUbicacion::class);
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
