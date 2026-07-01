<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfiguracionAuditoria extends Model
{
    use HasFactory;

    protected $table = 'configuracion_auditorias';

    protected $fillable = [
        'user_id',
        'entidad',
        'entidad_id',
        'accion',
        'cambios',
    ];

    protected function casts(): array
    {
        return [
            'cambios' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
