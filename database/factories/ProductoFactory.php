<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\NegocioAfiliado;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $nombre = fake()->unique()->words(3, true);

        return [
            'negocio_afiliado_id' => NegocioAfiliado::factory(),
            'categoria_id' => Categoria::factory(),
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre).'-'.fake()->unique()->numberBetween(100, 999),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 8, 80),
            'estado' => Producto::ESTADO_ACTIVO,
            'disponible' => true,
        ];
    }
}
