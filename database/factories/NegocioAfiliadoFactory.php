<?php

namespace Database\Factories;

use App\Models\NegocioAfiliado;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NegocioAfiliadoFactory extends Factory
{
    protected $model = NegocioAfiliado::class;

    public function definition(): array
    {
        $nombre = fake()->company();

        return [
            'nombre_comercial' => $nombre,
            'slug' => Str::slug($nombre).'-'.fake()->unique()->numberBetween(100, 999),
            'tipo_negocio' => fake()->randomElement(['restaurante', 'cafeteria', 'polleria', 'pizzeria', 'bodega']),
            'telefono' => fake()->numerify('9########'),
            'email' => fake()->safeEmail(),
            'direccion' => fake()->streetAddress(),
            'descripcion' => fake()->sentence(),
            'estado' => NegocioAfiliado::ESTADO_ACTIVO,
            'abierto' => true,
            'horarios' => ['lunes' => '09:00-22:00'],
        ];
    }
}
