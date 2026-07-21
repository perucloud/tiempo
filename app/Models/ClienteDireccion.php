<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteDireccion extends Model
{
    protected $table = 'cliente_direcciones';

    protected $fillable = [
        'cliente_id', 'alias',
        'nombre_receptor', 'celular_receptor', 'puede_recibir_otra_persona', 'instrucciones',
        'direccion_exacta', 'departamento', 'urbanizacion', 'distrito', 'provincia', 'region', 'referencia',
        'latitud', 'longitud', 'es_predeterminada',
    ];

    protected function casts(): array
    {
        return [
            'puede_recibir_otra_persona' => 'boolean',
            'es_predeterminada'          => 'boolean',
            'latitud'                    => 'float',
            'longitud'                   => 'float',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function etiqueta(): string
    {
        return $this->alias . ' — ' . $this->direccion_exacta;
    }
}
