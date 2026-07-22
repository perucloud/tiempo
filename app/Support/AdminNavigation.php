<?php

namespace App\Support;

use App\Models\User;

class AdminNavigation
{
    /**
     * Definición completa de módulos con su clave, label, ícono y ruta.
     */
    public static function allModules(): array
    {
        return [
            ['key' => 'dashboard',      'label' => 'Dashboard',          'icon' => 'bi-speedometer2',   'url' => route('admin.dashboard'),             'superadmin_only' => false],
            ['key' => 'pedidos',        'label' => 'Pedidos',            'icon' => 'bi-bag-check',      'url' => route('admin.orders.index'),          'superadmin_only' => false],
            ['key' => 'pagos',          'label' => 'Pagos',              'icon' => 'bi-credit-card',    'url' => route('admin.payments.index'),        'superadmin_only' => false],
            ['key' => 'repartidores',   'label' => 'Repartidores',       'icon' => 'bi-bicycle',        'url' => route('admin.couriers.index'),        'superadmin_only' => false],
            ['key' => 'clientes',       'label' => 'Clientes',           'icon' => 'bi-people',         'url' => route('admin.clients.index'),         'superadmin_only' => false],
            ['key' => 'negocios',       'label' => 'Negocios afiliados', 'icon' => 'bi-shop',           'url' => route('admin.businesses.index'),      'superadmin_only' => false],
            ['key' => 'categorias',     'label' => 'Categorias',         'icon' => 'bi-tags',           'url' => route('admin.categories.index'),      'superadmin_only' => false],
            ['key' => 'productos',      'label' => 'Productos',          'icon' => 'bi-box-seam',       'url' => route('admin.products.index'),        'superadmin_only' => false],
            ['key' => 'reportes',       'label' => 'Reportes',           'icon' => 'bi-bar-chart-line', 'url' => route('admin.reports.index'),         'superadmin_only' => false],
            ['key' => 'notificaciones', 'label' => 'Notificaciones',     'icon' => 'bi-bell',           'url' => route('admin.notifications.index'),   'superadmin_only' => false],
            ['key' => 'usuarios',       'label' => 'Usuarios',           'icon' => 'bi-person-gear',    'url' => route('admin.users.index'),           'superadmin_only' => true],
            ['key' => 'configuracion',  'label' => 'Configuracion',      'icon' => 'bi-gear',           'url' => route('admin.settings.index'),        'superadmin_only' => true],
        ];
    }

    /**
     * Lista de módulos asignables a admins/operadores (excluye los exclusivos de superadmin).
     */
    public static function assignableList(): array
    {
        return array_filter(
            self::allModules(),
            fn (array $m): bool => ! $m['superadmin_only'],
        );
    }

    /**
     * Módulos visibles para el usuario dado, marcando el activo.
     */
    public static function for(User $user, string $active = 'dashboard'): array
    {
        $visible = array_filter(
            self::allModules(),
            fn (array $module): bool => $user->hasModuleAccess($module['key']),
        );

        return array_map(
            fn (array $module): array => $module + ['active' => $module['key'] === $active],
            array_values($visible),
        );
    }

    /**
     * Compatibilidad hacia atrás para controladores que aún no pasan $user.
     * @deprecated Usar for(auth()->user(), $active)
     */
    public static function modules(string $active = 'dashboard'): array
    {
        return self::for(auth()->user(), $active);
    }
}
