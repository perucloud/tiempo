<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_INACTIVO = 'inactivo';

    public const TIPO_PRODUCTO = 'producto';

    public const TIPO_NEGOCIO = 'negocio';

    public const TIPO_PROMOCION = 'promocion';

    public const ESTADOS = [
        self::ESTADO_ACTIVO,
        self::ESTADO_INACTIVO,
    ];

    public const TIPOS = [
        self::TIPO_PRODUCTO,
        self::TIPO_NEGOCIO,
        self::TIPO_PROMOCION,
    ];

    protected $fillable = ['nombre', 'slug', 'tipo', 'estado', 'orden'];

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
            self::TIPO_PRODUCTO => 'Producto',
            self::TIPO_NEGOCIO => 'Negocio',
            self::TIPO_PROMOCION => 'Promocion',
        ];
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
