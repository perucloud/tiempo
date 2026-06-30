<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_APROBADO = 'aprobado';

    public const ESTADO_RECHAZADO = 'rechazado';

    protected $fillable = [
        'pedido_id',
        'verificado_por',
        'metodo',
        'monto',
        'estado',
        'voucher_path',
        'codigo_operacion',
        'observacion',
        'verificado_at',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'verificado_at' => 'datetime',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function verificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }
}
