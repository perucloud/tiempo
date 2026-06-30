<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NegocioAfiliado extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_INACTIVO = 'inactivo';

    public const TIPO_RESTAURANTE = 'restaurante';

    public const TIPO_CAFETERIA = 'cafeteria';

    public const TIPO_POLLERIA = 'polleria';

    public const TIPO_PIZZERIA = 'pizzeria';

    public const TIPO_LICORERIA = 'licoreria';

    public const TIPO_BODEGA = 'bodega';

    public const TIPO_FARMACIA = 'farmacia';

    public const TIPO_OTRO = 'otro';

    public const ESTADOS = [
        self::ESTADO_ACTIVO,
        self::ESTADO_INACTIVO,
    ];

    public const TIPOS = [
        self::TIPO_RESTAURANTE,
        self::TIPO_CAFETERIA,
        self::TIPO_POLLERIA,
        self::TIPO_PIZZERIA,
        self::TIPO_LICORERIA,
        self::TIPO_BODEGA,
        self::TIPO_FARMACIA,
        self::TIPO_OTRO,
    ];

    protected $table = 'negocios_afiliados';

    protected $fillable = [
        'user_id',
        'nombre_comercial',
        'slug',
        'tipo_negocio',
        'ruc',
        'telefono',
        'email',
        'direccion',
        'descripcion',
        'estado',
        'abierto',
        'horarios',
    ];

    protected function casts(): array
    {
        return [
            'abierto' => 'boolean',
            'horarios' => 'array',
        ];
    }

    public static function estadoOptions(): array
    {
        return [
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_INACTIVO => 'Inactivo',
        ];
    }

    public static function tipoOptions(): array
    {
        return [
            self::TIPO_RESTAURANTE => 'Restaurante',
            self::TIPO_CAFETERIA => 'Cafeteria',
            self::TIPO_POLLERIA => 'Polleria',
            self::TIPO_PIZZERIA => 'Pizzeria',
            self::TIPO_LICORERIA => 'Licoreria',
            self::TIPO_BODEGA => 'Bodega',
            self::TIPO_FARMACIA => 'Farmacia',
            self::TIPO_OTRO => 'Otro',
        ];
    }

    public function horariosTexto(): string
    {
        if (! is_array($this->horarios) || $this->horarios === []) {
            return 'Sin horarios definidos';
        }

        return collect($this->horarios)
            ->map(fn (string $value, string $key): string => ucfirst($key).': '.$value)
            ->implode(' | ');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
}
