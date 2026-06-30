<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'adminModules' => $this->adminModules(),
            'stats' => [
                ['label' => 'Pedidos nuevos', 'value' => '0', 'note' => 'Pendientes de revision'],
                ['label' => 'Pagos por verificar', 'value' => '0', 'note' => 'Yape/Plin en espera'],
                ['label' => 'Repartidores activos', 'value' => '0', 'note' => 'Disponibilidad del dia'],
                ['label' => 'Ventas de hoy', 'value' => 'S/ 0.00', 'note' => 'Corte operativo'],
            ],
            'mobileTasks' => [
                ['label' => 'Pedidos en curso', 'note' => 'Ver y cambiar estados', 'badge' => 'Prioridad'],
                ['label' => 'Pagos pendientes', 'note' => 'Aprobar o rechazar voucher', 'badge' => 'Rapido'],
                ['label' => 'Repartidores', 'note' => 'Consultar disponibilidad', 'badge' => 'Ruta'],
                ['label' => 'Ventas rapidas', 'note' => 'Resumen del dia', 'badge' => 'Hoy'],
            ],
            'recentOrders' => [
                ['code' => 'PED-0001', 'customer' => 'Sin pedidos registrados', 'status' => 'pendiente', 'payment' => 'pendiente'],
            ],
        ]);
    }

    private function adminModules(): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => 'D', 'url' => route('admin.dashboard'), 'active' => true],
            ['label' => 'Pedidos', 'icon' => 'P', 'url' => '#'],
            ['label' => 'Clientes', 'icon' => 'C', 'url' => '#'],
            ['label' => 'Categorias', 'icon' => 'G', 'url' => '#'],
            ['label' => 'Negocios afiliados', 'icon' => 'N', 'url' => '#'],
            ['label' => 'Productos', 'icon' => 'R', 'url' => '#'],
            ['label' => 'Repartidores', 'icon' => 'M', 'url' => '#'],
            ['label' => 'Pagos', 'icon' => 'Y', 'url' => '#'],
            ['label' => 'Reportes', 'icon' => 'T', 'url' => '#'],
            ['label' => 'Usuarios', 'icon' => 'U', 'url' => '#'],
            ['label' => 'Configuracion', 'icon' => 'S', 'url' => '#'],
        ];
    }
}
