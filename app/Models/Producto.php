<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_INACTIVO = 'inactivo';

    protected $fillable = [
        'negocio_afiliado_id',
        'categoria_id',
        'nombre',
        'slug',
        'descripcion',
        'precio',
        'precio_promocional',
        'imagen',
        'estado',
        'disponible',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'precio_promocional' => 'decimal:2',
            'disponible' => 'boolean',
        ];
    }

    public function negocioAfiliado(): BelongsTo
    {
        return $this->belongsTo(NegocioAfiliado::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function pedidoDetalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}
