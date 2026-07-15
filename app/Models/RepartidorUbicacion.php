<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepartidorUbicacion extends Model
{
    public $timestamps = false;

    protected $table = 'repartidor_ubicaciones';

    protected $fillable = [
        'repartidor_id',
        'pedido_id',
        'latitud',
        'longitud',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'latitud'    => 'decimal:7',
            'longitud'   => 'decimal:7',
            'created_at' => 'datetime',
        ];
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(Repartidor::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
