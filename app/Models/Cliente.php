<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Cliente extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $guard = 'cliente';

    public const ESTADO_ACTIVO   = 'activo';
    public const ESTADO_INACTIVO = 'inactivo';
    public const ESTADOS = [self::ESTADO_ACTIVO, self::ESTADO_INACTIVO];

    public const SEXOS = [
        'masculino'         => 'Masculino',
        'femenino'          => 'Femenino',
        'otro'              => 'Otro',
        'prefiero_no_decir' => 'Prefiero no decirlo',
    ];

    public const TIPOS_DOCUMENTO = [
        'DNI' => 'DNI',
        'CE'  => 'Carnet de extranjería',
    ];

    public const PREFERENCIAS_PAGO = [
        'yape'     => 'Yape',
        'plin'     => 'Plin',
        'tarjeta'  => 'Tarjeta débito',
        'efectivo' => 'Efectivo contra entrega',
    ];

    protected $fillable = [
        'user_id', 'codigo_cliente',
        'nombres', 'apellidos', 'telefono', 'whatsapp', 'email', 'email_verified_at',
        'documento', 'tipo_documento', 'fecha_nacimiento', 'sexo',
        'password', 'remember_token',
        'estado', 'foto_perfil', 'idioma',
        'recibir_promociones', 'recibir_push', 'recibir_whatsapp', 'recibir_email',
        'preferencia_pago',
        'total_pedidos', 'total_gastado', 'ultimo_pedido_at',
        'ultimo_acceso', 'ip_ultimo_acceso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'             => 'hashed',
            'email_verified_at'    => 'datetime',
            'fecha_nacimiento'     => 'date',
            'ultimo_pedido_at'     => 'datetime',
            'ultimo_acceso'        => 'datetime',
            'recibir_promociones'  => 'boolean',
            'recibir_push'         => 'boolean',
            'recibir_whatsapp'     => 'boolean',
            'recibir_email'        => 'boolean',
            'puede_recibir_otra_persona' => 'boolean',
            'total_gastado'        => 'decimal:2',
        ];
    }

    /* ── Helpers ── */

    public static function estadoOptions(): array
    {
        return [self::ESTADO_ACTIVO => 'Activo', self::ESTADO_INACTIVO => 'Inactivo'];
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    public function iniciales(): string
    {
        $parts = array_filter(explode(' ', $this->nombreCompleto()));
        return strtoupper(implode('', array_map(fn ($p) => $p[0], array_slice($parts, 0, 2))));
    }

    public function tienePerfil(): bool
    {
        return ! empty($this->tipo_documento) && ! empty($this->fecha_nacimiento);
    }

    /* ── Relaciones ── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(ClienteDireccion::class);
    }

    public function direccionPredeterminada(): ?ClienteDireccion
    {
        return $this->direcciones()->where('es_predeterminada', true)->first();
    }
}
