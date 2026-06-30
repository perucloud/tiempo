<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_INACTIVO = 'inactivo';

    protected $fillable = ['nombre', 'slug', 'tipo', 'estado', 'orden'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
