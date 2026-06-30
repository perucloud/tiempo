<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminNavigation;
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
        return AdminNavigation::for('dashboard');
    }
}
