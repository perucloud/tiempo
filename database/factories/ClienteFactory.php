<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'telefono' => fake()->numerify('9########'),
            'email' => fake()->safeEmail(),
            'documento' => fake()->numerify('########'),
            'estado' => Cliente::ESTADO_ACTIVO,
        ];
    }
}
