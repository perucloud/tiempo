<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [User::ROLE_SUPERADMIN, 'SuperAdmin', 'Control absoluto del sistema.'],
            [User::ROLE_ADMIN, 'Admin', 'Control administrativo segun permisos.'],
            [User::ROLE_OPERADOR, 'Operador', 'Operacion diaria de pedidos, pagos y repartidores.'],
            [User::ROLE_NEGOCIO_AFILIADO, 'Negocio Afiliado', 'Gestion limitada a su negocio y carta.'],
            [User::ROLE_REPARTIDOR, 'Repartidor', 'Acceso a pedidos asignados y estados de entrega.'],
            [User::ROLE_CLIENTE, 'Cliente', 'Compra y seguimiento desde la app movil.'],
        ];

        foreach ($roles as [$code, $name, $description]) {
            Role::query()->updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'description' => $description],
            );
        }
    }
}
