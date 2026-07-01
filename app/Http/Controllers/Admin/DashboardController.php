<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Support\AdminNavigation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->startOfDay();
        $recentOrders = Pedido::query()
            ->with(['cliente:id,nombres,apellidos,telefono'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'adminModules' => $this->adminModules(),
            'stats' => [
                [
                    'label' => 'Pedidos nuevos',
                    'value' => Pedido::query()->where('estado', Pedido::ESTADO_PENDIENTE)->count(),
                    'note' => 'Pendientes de revision',
                ],
                [
                    'label' => 'Pagos por verificar',
                    'value' => Pago::query()->where('estado', Pago::ESTADO_PENDIENTE)->count(),
                    'note' => 'Yape/Plin en espera',
                ],
                [
                    'label' => 'Repartidores activos',
                    'value' => Repartidor::query()->where('estado', Repartidor::ESTADO_DISPONIBLE)->count(),
                    'note' => 'Disponibilidad actual',
                ],
                [
                    'label' => 'Ventas de hoy',
                    'value' => 'S/ '.number_format((float) Pedido::query()
                        ->where('estado', Pedido::ESTADO_ENTREGADO)
                        ->where('created_at', '>=', $today)
                        ->sum('total'), 2),
                    'note' => 'Corte operativo',
                ],
            ],
            'mobileTasks' => [
                ['label' => 'Pedidos en curso', 'note' => 'Ver y cambiar estados', 'badge' => 'Prioridad'],
                ['label' => 'Pagos pendientes', 'note' => 'Aprobar o rechazar voucher', 'badge' => 'Rapido'],
                ['label' => 'Repartidores', 'note' => 'Consultar disponibilidad', 'badge' => 'Ruta'],
                ['label' => 'Ventas rapidas', 'note' => 'Resumen del dia', 'badge' => 'Hoy'],
            ],
            'recentOrders' => $recentOrders->map(fn (Pedido $order): array => [
                'code' => $order->codigo,
                'customer' => $order->cliente?->nombreCompleto() ?: $order->cliente?->telefono ?: 'Sin cliente',
                'status' => $order->estadoLabel(),
                'payment' => $order->estado_pago,
            ])->all() ?: [
                ['code' => 'Sin pedidos', 'customer' => 'Sin pedidos registrados', 'status' => 'pendiente', 'payment' => 'pendiente'],
            ],
        ]);
    }

    private function adminModules(): array
    {
        return AdminNavigation::for('dashboard');
    }
}
