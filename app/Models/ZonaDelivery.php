<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZonaDelivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zonas_delivery';

    protected $fillable = [
        'nombre',
        'descripcion_cobertura',
        'costo_delivery',
        'pedido_minimo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'costo_delivery' => 'decimal:2',
            'pedido_minimo' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }
}
