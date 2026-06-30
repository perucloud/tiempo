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
