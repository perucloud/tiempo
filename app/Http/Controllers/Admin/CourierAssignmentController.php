<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignCourierRequest;
use App\Models\Pedido;
use App\Models\Repartidor;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;

class CourierAssignmentController extends Controller
{
    public function update(AssignCourierRequest $request, Pedido $order, NotificationService $notifications): RedirectResponse
    {
        $data = $request->validated();
        $previous = $order->estado;
        $previousCourier = $order->repartidor;
        $courier = Repartidor::query()->findOrFail($data['repartidor_id']);

        $order->update([
            'repartidor_id' => $courier->id,
            'estado' => Pedido::ESTADO_ASIGNADO,
        ]);

        if ($previousCourier && $previousCourier->id !== $courier->id) {
            $previousCourier->update(['estado' => Repartidor::ESTADO_DISPONIBLE]);
        }

        $courier->update(['estado' => Repartidor::ESTADO_OCUPADO]);

        $order->estados()->create([
            'user_id' => $request->user()->id,
            'estado_anterior' => $previous,
            'estado_nuevo' => Pedido::ESTADO_ASIGNADO,
            'comentario' => $data['comentario'] ?? 'Repartidor asignado.',
        ]);
        $notifications->orderStatusChanged($order->refresh(), $previous);
        $notifications->courierAssigned($order, $courier);

        return redirect()
            ->route('admin.orders.edit', $order)
            ->with('status', 'Repartidor asignado correctamente.');
    }
}
