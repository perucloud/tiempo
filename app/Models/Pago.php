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

    public const METODO_YAPE = 'yape';

    public const METODO_PLIN = 'plin';

    public const METODOS = [
        self::METODO_YAPE,
        self::METODO_PLIN,
    ];

    public const ESTADOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_APROBADO,
        self::ESTADO_RECHAZADO,
    ];

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

    public static function metodoOptions(): array
    {
        return [
            self::METODO_YAPE => 'Yape',
            self::METODO_PLIN => 'Plin',
        ];
    }

    public static function estadoOptions(): array
    {
        return [
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_APROBADO => 'Aprobado',
            self::ESTADO_RECHAZADO => 'Rechazado',
        ];
    }

    public function estadoLabel(): string
    {
        return self::estadoOptions()[$this->estado] ?? ucfirst($this->estado);
    }

    public function verificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }
}
