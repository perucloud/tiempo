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

    public const ESTADO_SUSPENDIDO = 'suspendido';

    public const ESTADO_VACACIONES = 'vacaciones';

    public const ESTADO_CERRADO_TEMP = 'cerrado_temporalmente';

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
        self::ESTADO_SUSPENDIDO,
        self::ESTADO_VACACIONES,
        self::ESTADO_CERRADO_TEMP,
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
        'imagen',
        'slogan',
        'precio_minimo',
        'color_marca',
        'estado',
        'abierto',
        'horarios',
        'hora_apertura',
        'hora_cierre',
        'tiempo_preparacion',
        'departamento',
        'provincia',
        'distrito',
        'codigo_postal',
        'pais',
        'referencia',
        'latitud',
        'longitud',
        'celular',
        'whatsapp',
        'telefono_fijo',
        'pagina_web',
        'facebook',
        'instagram',
        'tiktok',
    ];

    private const TIPO_COLORES = [
        'restaurante' => '#CC3D00',
        'cafeteria'   => '#5A3E1B',
        'polleria'    => '#5E1A7A',
        'pizzeria'    => '#8B0000',
        'licoreria'   => '#0E5C1A',
        'bodega'      => '#0A4D6E',
        'farmacia'    => '#006B6B',
        'otro'        => '#2D2D2D',
    ];

    public function colorEfectivo(): string
    {
        return $this->color_marca ?? self::TIPO_COLORES[$this->tipo_negocio] ?? '#CC3D00';
    }

    protected function casts(): array
    {
        return [
            'abierto'            => 'boolean',
            'horarios'           => 'array',
            'latitud'            => 'float',
            'longitud'           => 'float',
            'tiempo_preparacion' => 'integer',
        ];
    }

    public static function estadoOptions(): array
    {
        return [
            self::ESTADO_ACTIVO       => 'Activo',
            self::ESTADO_INACTIVO     => 'Inactivo',
            self::ESTADO_SUSPENDIDO   => 'Suspendido',
            self::ESTADO_VACACIONES   => 'Vacaciones',
            self::ESTADO_CERRADO_TEMP => 'Cerrado temporalmente',
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
