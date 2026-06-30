<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard',
            'pedidos',
            'clientes',
            'categorias',
            'negocios_afiliados',
            'productos',
            'repartidores',
            'pagos',
            'reportes',
            'usuarios',
            'configuracion',
        ];

        $actions = ['view', 'create', 'update', 'delete'];

        $permissions = collect($modules)
            ->flatMap(fn (string $module) => collect($actions)->map(fn (string $action) => [
                'code' => "{$module}.{$action}",
                'module' => $module,
                'action' => $action,
                'name' => ucfirst(str_replace('_', ' ', $module))." {$action}",
            ]));

        $permissions->each(fn (array $permission) => Permission::query()->updateOrCreate(
            ['code' => $permission['code']],
            $permission,
        ));

        $superadmin = Role::query()->where('code', User::ROLE_SUPERADMIN)->first();

        if ($superadmin) {
            $superadmin->permissions()->sync(Permission::query()->pluck('id')->all());
        }

        $operator = Role::query()->where('code', User::ROLE_OPERADOR)->first();

        if ($operator) {
            $operator->permissions()->sync(
                Permission::query()
                    ->whereIn('module', ['dashboard', 'pedidos', 'pagos', 'repartidores'])
                    ->pluck('id')
                    ->all(),
            );
        }

        $affiliate = Role::query()->where('code', User::ROLE_NEGOCIO_AFILIADO)->first();

        if ($affiliate) {
            $affiliate->permissions()->sync(
                Permission::query()
                    ->whereIn('code', [
                        'dashboard.view',
                        'categorias.view',
                        'productos.view',
                        'productos.create',
                        'productos.update',
                    ])
                    ->pluck('id')
                    ->all(),
            );
        }
    }
}
