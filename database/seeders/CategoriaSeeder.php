<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = ['Comidas', 'Bebidas', 'Promociones'];

        foreach ($categorias as $index => $nombre) {
            Categoria::query()->updateOrCreate(
                ['slug' => Str::slug($nombre)],
                [
                    'nombre' => $nombre,
                    'tipo' => 'producto',
                    'estado' => Categoria::ESTADO_ACTIVO,
                    'orden' => $index + 1,
                ],
            );
        }
    }
}
