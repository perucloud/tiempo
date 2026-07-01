<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    use HasFactory;

    public const CANAL_INTERNO = 'interno';

    public const DESTINATARIO_ADMIN = 'admin';

    public const DESTINATARIO_CLIENTE = 'cliente';

    public const DESTINATARIO_REPARTIDOR = 'repartidor';

    public const TIPO_PAGO_APROBADO = 'pago_aprobado';

    public const TIPO_PAGO_RECHAZADO = 'pago_rechazado';

    public const TIPO_PEDIDO_ESTADO = 'pedido_estado';

    public const TIPO_REPARTIDOR_ASIGNADO = 'repartidor_asignado';

    protected $table = 'notificaciones';

    protected $fillable = [
        'tipo',
        'canal',
        'destinatario_tipo',
        'user_id',
        'cliente_id',
        'repartidor_id',
        'pedido_id',
        'pago_id',
        'titulo',
        'mensaje',
        'data',
        'leido_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'leido_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(Repartidor::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function isUnread(): bool
    {
        return $this->leido_at === null;
    }
}
