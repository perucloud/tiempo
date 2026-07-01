<?php

namespace App\Support;

class AdminNavigation
{
    public static function modules(string $active = 'dashboard'): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'D', 'url' => route('admin.dashboard')],
            ['key' => 'pedidos', 'label' => 'Pedidos', 'icon' => 'P', 'url' => '#'],
            ['key' => 'clientes', 'label' => 'Clientes', 'icon' => 'C', 'url' => route('admin.clients.index')],
            ['key' => 'categorias', 'label' => 'Categorias', 'icon' => 'G', 'url' => route('admin.categories.index')],
            ['key' => 'negocios', 'label' => 'Negocios afiliados', 'icon' => 'N', 'url' => route('admin.businesses.index')],
            ['key' => 'productos', 'label' => 'Productos', 'icon' => 'R', 'url' => route('admin.products.index')],
            ['key' => 'repartidores', 'label' => 'Repartidores', 'icon' => 'M', 'url' => '#'],
            ['key' => 'pagos', 'label' => 'Pagos', 'icon' => 'Y', 'url' => '#'],
            ['key' => 'reportes', 'label' => 'Reportes', 'icon' => 'T', 'url' => '#'],
            ['key' => 'usuarios', 'label' => 'Usuarios', 'icon' => 'U', 'url' => route('admin.users.index')],
            ['key' => 'configuracion', 'label' => 'Configuracion', 'icon' => 'S', 'url' => '#'],
        ];
    }

    public static function for(string $active = 'dashboard'): array
    {
        return array_map(
            fn (array $module): array => $module + ['active' => $module['key'] === $active],
            self::modules($active),
        );
    }
}
