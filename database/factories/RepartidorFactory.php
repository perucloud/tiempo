<?php

namespace Database\Factories;

use App\Models\Repartidor;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepartidorFactory extends Factory
{
    protected $model = Repartidor::class;

    public function definition(): array
    {
        return [
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'telefono' => fake()->numerify('9########'),
            'documento' => fake()->numerify('########'),
            'vehiculo_tipo' => fake()->randomElement(['moto', 'bicicleta']),
            'vehiculo_placa' => strtoupper(fake()->bothify('??-###')),
            'estado' => Repartidor::ESTADO_DISPONIBLE,
        ];
    }
}
