<?php

namespace App\Observers;

use App\Models\Pedido;

class PedidoObserver
{
    public function created(Pedido $pedido): void
    {
        $this->updateClienteStats($pedido);
    }

    public function updated(Pedido $pedido): void
    {
        if ($pedido->wasChanged('estado') && $pedido->estado === Pedido::ESTADO_ENTREGADO) {
            $this->updateClienteStats($pedido);
        }
    }

    private function updateClienteStats(Pedido $pedido): void
    {
        $cliente = $pedido->cliente;
        if (! $cliente) {
            return;
        }

        $stats = $cliente->pedidos()
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(total), 0) as gastado, MAX(created_at) as ultimo')
            ->first();

        $cliente->updateQuietly([
            'total_pedidos'   => (int) $stats->total,
            'total_gastado'   => number_format((float) $stats->gastado, 2, '.', ''),
            'ultimo_pedido_at' => $stats->ultimo,
        ]);
    }
}
