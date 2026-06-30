<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        $nombre = fake()->unique()->words(2, true);

        return [
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre).'-'.fake()->unique()->numberBetween(100, 999),
            'tipo' => 'producto',
            'estado' => Categoria::ESTADO_ACTIVO,
            'orden' => fake()->numberBetween(1, 20),
        ];
    }
}
