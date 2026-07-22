<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportFilterRequest;
use App\Models\NegocioAfiliado;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Support\AdminNavigation;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(ReportFilterRequest $request): View
    {
        $filters = $this->filters($request);
        $orders = $this->ordersBetween($filters['date_from'], $filters['date_to']);
        $payments = $this->paymentsBetween($filters['date_from'], $filters['date_to']);

        return view('admin.reports.index', [
            'adminModules' => AdminNavigation::for(auth()->user(), 'reportes'),
            'filters' => [
                'date_from' => $filters['date_from']->toDateString(),
                'date_to' => $filters['date_to']->toDateString(),
            ],
            'summary' => [
                'orders' => (clone $orders)->count(),
                'delivered_orders' => (clone $orders)->where('estado', Pedido::ESTADO_ENTREGADO)->count(),
                'sales_total' => (float) (clone $orders)->where('estado', Pedido::ESTADO_ENTREGADO)->sum('total'),
                'payments_total' => (float) (clone $payments)->where('estado', Pago::ESTADO_APROBADO)->sum('monto'),
                'pending_payments' => (clone $payments)->where('estado', Pago::ESTADO_PENDIENTE)->count(),
                'active_businesses' => NegocioAfiliado::query()->where('estado', NegocioAfiliado::ESTADO_ACTIVO)->count(),
                'available_couriers' => Repartidor::query()->where('estado', Repartidor::ESTADO_DISPONIBLE)->count(),
            ],
            'ordersByStatus' => (clone $orders)
                ->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado')
                ->all(),
            'paymentsByMethod' => (clone $payments)
                ->selectRaw('metodo, COUNT(*) as total, COALESCE(SUM(monto), 0) as amount')
                ->groupBy('metodo')
                ->get(),
            'topBusinesses' => (clone $orders)
                ->selectRaw('negocio_afiliado_id, COUNT(*) as orders_count, COALESCE(SUM(total), 0) as sales_total')
                ->where('estado', Pedido::ESTADO_ENTREGADO)
                ->with('negocioAfiliado')
                ->groupBy('negocio_afiliado_id')
                ->orderByDesc('sales_total')
                ->limit(5)
                ->get(),
            'courierPerformance' => (clone $orders)
                ->selectRaw('repartidor_id, COUNT(*) as delivered_count')
                ->where('estado', Pedido::ESTADO_ENTREGADO)
                ->whereNotNull('repartidor_id')
                ->with('repartidor')
                ->groupBy('repartidor_id')
                ->orderByDesc('delivered_count')
                ->limit(5)
                ->get(),
            'estadoOptions' => Pedido::estadoOptions(),
            'metodoOptions' => Pago::metodoOptions(),
        ]);
    }

    private function filters(ReportFilterRequest $request): array
    {
        return [
            'date_from' => $request->date('date_from')
                ? CarbonImmutable::parse($request->date('date_from'))->startOfDay()
                : now()->startOfMonth()->toImmutable(),
            'date_to' => $request->date('date_to')
                ? CarbonImmutable::parse($request->date('date_to'))->endOfDay()
                : now()->endOfDay()->toImmutable(),
        ];
    }

    private function ordersBetween(CarbonImmutable $from, CarbonImmutable $to): Builder
    {
        return Pedido::query()->whereBetween('created_at', [$from, $to]);
    }

    private function paymentsBetween(CarbonImmutable $from, CarbonImmutable $to): Builder
    {
        return Pago::query()->whereBetween('created_at', [$from, $to]);
    }
}
