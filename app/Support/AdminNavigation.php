<?php

namespace App\Support;

class AdminNavigation
{
    public static function modules(string $active = 'dashboard'): array
    {
        return [
            ['key' => 'dashboard',      'label' => 'Dashboard',          'icon' => 'bi-speedometer2',       'url' => route('admin.dashboard')],
            ['key' => 'pedidos',        'label' => 'Pedidos',            'icon' => 'bi-bag-check',          'url' => route('admin.orders.index')],
            ['key' => 'pagos',          'label' => 'Pagos',              'icon' => 'bi-credit-card',        'url' => route('admin.payments.index')],
            ['key' => 'repartidores',   'label' => 'Repartidores',       'icon' => 'bi-bicycle',            'url' => route('admin.couriers.index')],
            ['key' => 'clientes',       'label' => 'Clientes',           'icon' => 'bi-people',             'url' => route('admin.clients.index')],
            ['key' => 'negocios',       'label' => 'Negocios afiliados', 'icon' => 'bi-shop',               'url' => route('admin.businesses.index')],
            ['key' => 'categorias',     'label' => 'Categorias',         'icon' => 'bi-tags',               'url' => route('admin.categories.index')],
            ['key' => 'productos',      'label' => 'Productos',          'icon' => 'bi-box-seam',           'url' => route('admin.products.index')],
            ['key' => 'reportes',       'label' => 'Reportes',           'icon' => 'bi-bar-chart-line',     'url' => route('admin.reports.index')],
            ['key' => 'notificaciones', 'label' => 'Notificaciones',     'icon' => 'bi-bell',               'url' => route('admin.notifications.index')],
            ['key' => 'usuarios',       'label' => 'Usuarios',           'icon' => 'bi-person-gear',        'url' => route('admin.users.index')],
            ['key' => 'configuracion',  'label' => 'Configuracion',      'icon' => 'bi-gear',               'url' => route('admin.settings.index')],
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
