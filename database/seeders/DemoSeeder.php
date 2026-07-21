<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\NegocioAfiliado;
use App\Models\Producto;
use App\Models\Repartidor;
use App\Models\Role;
use App\Models\User;
use App\Models\ZonaDelivery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Zona de delivery ──────────────────────────────────────────────
        ZonaDelivery::query()->updateOrCreate(
            ['nombre' => 'Zona Centro'],
            [
                'polygon' => [
                    [-76.9240, -12.0600],
                    [-76.8800, -12.0600],
                    [-76.8800, -12.1000],
                    [-76.9240, -12.1000],
                ],
                'costo_delivery'      => 5.00,
                'km_incluidos'        => 3,
                'precio_por_km_extra' => 1.50,
                'prioridad'           => 1,
                'activo'              => true,
            ]
        );

        // ── Repartidor ────────────────────────────────────────────────────
        $userRepartidor = User::query()->updateOrCreate(
            ['email' => 'repartidor@tiempo.test'],
            [
                'name'     => 'Carlos Repartidor',
                'password' => Hash::make('12345678'),
                'role'     => User::ROLE_REPARTIDOR,
                'role_id'  => Role::query()->where('code', User::ROLE_REPARTIDOR)->value('id'),
                'status'   => User::STATUS_ACTIVE,
            ]
        );

        Repartidor::query()->updateOrCreate(
            ['user_id' => $userRepartidor->id],
            [
                'nombres'          => 'Carlos',
                'apellidos'        => 'Quispe Mamani',
                'telefono'         => '987654321',
                'documento'        => '45678901',
                'vehiculo_tipo'    => 'moto',
                'vehiculo_placa'   => 'ABC-123',
                'estado'           => Repartidor::ESTADO_DISPONIBLE,
                'estado_operativo' => Repartidor::OP_AVAILABLE,
                'latitud_actual'   => -12.0700,
                'longitud_actual'  => -76.9000,
            ]
        );

        // ── Usuario negocio afiliado ──────────────────────────────────────
        $userNegocio = User::query()->updateOrCreate(
            ['email' => 'negocio@tiempo.test'],
            [
                'name'     => 'La Brasa Dorada',
                'password' => Hash::make('12345678'),
                'role'     => User::ROLE_NEGOCIO_AFILIADO,
                'role_id'  => Role::query()->where('code', User::ROLE_NEGOCIO_AFILIADO)->value('id'),
                'status'   => User::STATUS_ACTIVE,
            ]
        );

        // ── Negocio afiliado ──────────────────────────────────────────────
        $negocio = NegocioAfiliado::query()->updateOrCreate(
            ['slug' => 'la-brasa-dorada'],
            [
                'user_id'           => $userNegocio->id,
                'nombre_comercial'  => 'La Brasa Dorada',
                'slug'              => 'la-brasa-dorada',
                'tipo_negocio'      => 'polleria',
                'ruc'               => '20123456789',
                'telefono'          => '014567890',
                'celular'           => '956789012',
                'email'             => 'negocio@tiempo.test',
                'direccion'         => 'Av. Javier Prado 1234, San Isidro',
                'descripcion'       => 'La mejor pollería de la zona. Pollos a la brasa y parrillas.',
                'slogan'            => 'El sabor que conquista',
                'estado'            => NegocioAfiliado::ESTADO_ACTIVO,
                'abierto'           => true,
                'tiempo_preparacion' => 25,
                'latitud'           => -12.0700,
                'longitud'          => -76.9000,
                'distrito'          => 'San Isidro',
                'provincia'         => 'Lima',
                'departamento'      => 'Lima',
            ]
        );

        // ── Segundo negocio ───────────────────────────────────────────────
        $negocio2 = NegocioAfiliado::query()->updateOrCreate(
            ['slug' => 'cafe-del-centro'],
            [
                'nombre_comercial'  => 'Café del Centro',
                'slug'              => 'cafe-del-centro',
                'tipo_negocio'      => 'cafeteria',
                'telefono'          => '014501234',
                'email'             => 'cafe@tiempo.test',
                'direccion'         => 'Jr. de la Unión 456, Lima',
                'descripcion'       => 'Café artesanal, sándwiches y postres.',
                'estado'            => NegocioAfiliado::ESTADO_ACTIVO,
                'abierto'           => true,
                'tiempo_preparacion' => 15,
                'latitud'           => -12.0650,
                'longitud'          => -76.8950,
                'distrito'          => 'Lima',
                'provincia'         => 'Lima',
                'departamento'      => 'Lima',
            ]
        );

        // ── Categorías de productos ───────────────────────────────────────
        $catComidas  = Categoria::query()->where('slug', 'comidas')->first();
        $catBebidas  = Categoria::query()->where('slug', 'bebidas')->first();
        $catPromos   = Categoria::query()->where('slug', 'promociones')->first();

        // ── Productos — La Brasa Dorada ───────────────────────────────────
        $productos1 = [
            ['nombre' => 'Pollo a la Brasa (1/4)',    'precio' => 18.00, 'categoria_id' => $catComidas?->id],
            ['nombre' => 'Pollo a la Brasa (1/2)',    'precio' => 32.00, 'categoria_id' => $catComidas?->id],
            ['nombre' => 'Pollo a la Brasa entero',   'precio' => 58.00, 'categoria_id' => $catComidas?->id],
            ['nombre' => 'Combo Familiar (pollo+papas+ensalada)', 'precio' => 75.00, 'precio_promocional' => 65.00, 'categoria_id' => $catPromos?->id],
            ['nombre' => 'Anticuchos (6 unid.)',       'precio' => 20.00, 'categoria_id' => $catComidas?->id],
            ['nombre' => 'Chicha Morada (1L)',         'precio' => 8.00,  'categoria_id' => $catBebidas?->id],
            ['nombre' => 'Inca Kola (600ml)',          'precio' => 5.00,  'categoria_id' => $catBebidas?->id],
        ];

        foreach ($productos1 as $data) {
            Producto::query()->updateOrCreate(
                ['negocio_afiliado_id' => $negocio->id, 'nombre' => $data['nombre']],
                array_merge($data, [
                    'negocio_afiliado_id' => $negocio->id,
                    'slug'       => Str::slug($data['nombre']) . '-' . $negocio->id,
                    'estado'     => Producto::ESTADO_ACTIVO,
                    'disponible' => true,
                ])
            );
        }

        // ── Productos — Café del Centro ───────────────────────────────────
        $productos2 = [
            ['nombre' => 'Café Americano',            'precio' => 7.00,  'categoria_id' => $catBebidas?->id],
            ['nombre' => 'Café Latte',                'precio' => 9.00,  'categoria_id' => $catBebidas?->id],
            ['nombre' => 'Capuccino',                 'precio' => 9.50,  'categoria_id' => $catBebidas?->id],
            ['nombre' => 'Sándwich de Pollo',         'precio' => 12.00, 'categoria_id' => $catComidas?->id],
            ['nombre' => 'Sándwich Mixto',            'precio' => 10.00, 'categoria_id' => $catComidas?->id],
            ['nombre' => 'Torta de Chocolate',        'precio' => 8.00,  'categoria_id' => $catPromos?->id],
            ['nombre' => 'Combo Desayuno (café+sand)', 'precio' => 18.00, 'precio_promocional' => 15.00, 'categoria_id' => $catPromos?->id],
        ];

        foreach ($productos2 as $data) {
            Producto::query()->updateOrCreate(
                ['negocio_afiliado_id' => $negocio2->id, 'nombre' => $data['nombre']],
                array_merge($data, [
                    'negocio_afiliado_id' => $negocio2->id,
                    'slug'       => Str::slug($data['nombre']) . '-' . $negocio2->id,
                    'estado'     => Producto::ESTADO_ACTIVO,
                    'disponible' => true,
                ])
            );
        }

        // ── Cliente de prueba (PWA) ───────────────────────────────────────
        Cliente::query()->updateOrCreate(
            ['telefono' => '999000111'],
            [
                'nombres'   => 'Ana',
                'apellidos' => 'García López',
                'telefono'  => '999000111',
                'email'     => 'cliente@tiempo.test',
                'password'  => Hash::make('12345678'),
                'estado'    => Cliente::ESTADO_ACTIVO,
            ]
        );

        $this->command->info('✓ Zona de delivery creada');
        $this->command->info('✓ Repartidor: repartidor@tiempo.test / 12345678');
        $this->command->info('✓ Negocio: negocio@tiempo.test / 12345678');
        $this->command->info('✓ Cliente PWA: tel. 999000111 / pass 12345678');
        $this->command->info('✓ 2 negocios + 14 productos creados');
    }
}
