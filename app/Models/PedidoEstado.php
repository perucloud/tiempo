<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoEstado extends Model
{
    use HasFactory;

    protected $table = 'pedido_estados';

    protected $fillable = ['pedido_id', 'user_id', 'estado_anterior', 'estado_nuevo', 'comentario'];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
