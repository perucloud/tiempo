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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
}
