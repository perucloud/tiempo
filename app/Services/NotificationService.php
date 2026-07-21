<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Repartidor;

class NotificationService
{
    public function __construct(private readonly WebPushService $push) {}

    public function paymentReviewed(Pago $payment): void
    {
        $payment->loadMissing('pedido.cliente');
        $order = $payment->pedido;
        $approved = $payment->estado === Pago::ESTADO_APROBADO;
        $type = $approved ? Notificacion::TIPO_PAGO_APROBADO : Notificacion::TIPO_PAGO_RECHAZADO;
        $title = $approved ? 'Pago aprobado' : 'Pago rechazado';
        $message = $approved
            ? "Tu pago del pedido {$order->codigo} fue aprobado."
            : "Tu pago del pedido {$order->codigo} fue rechazado. Revisa el comprobante.";

        $this->create([
            'tipo' => $type,
            'destinatario_tipo' => Notificacion::DESTINATARIO_CLIENTE,
            'cliente_id' => $order->cliente_id,
            'pedido_id' => $order->id,
            'pago_id' => $payment->id,
            'titulo' => $title,
            'mensaje' => $message,
        ]);

        $this->push->sendToCustomer($order->cliente_id, $title, $message, [
            'url' => route('app.orders.show', $order->codigo), 'pedido' => $order->codigo,
        ]);

        $this->create([
            'tipo' => $type,
            'destinatario_tipo' => Notificacion::DESTINATARIO_ADMIN,
            'pedido_id' => $order->id,
            'pago_id' => $payment->id,
            'titulo' => "Revision de pago {$order->codigo}",
            'mensaje' => "El pago del pedido {$order->codigo} quedo como {$payment->estadoLabel()}.",
        ]);
    }

    public function orderStatusChanged(Pedido $order, string $previousState): void
    {
        $order->loadMissing('cliente');

        $notification = $this->create([
            'tipo' => Notificacion::TIPO_PEDIDO_ESTADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_CLIENTE,
            'cliente_id' => $order->cliente_id,
            'pedido_id' => $order->id,
            'titulo' => 'Estado del pedido actualizado',
            'mensaje' => "Tu pedido {$order->codigo} ahora esta en estado: {$order->estadoLabel()}.",
            'data' => [
                'estado_anterior' => $previousState,
                'estado_nuevo' => $order->estado,
            ],
        ]);

        $this->push->sendToCustomer($order->cliente_id, $notification->titulo, $notification->mensaje, [
            'url' => route('app.orders.show', $order->codigo), 'pedido' => $order->codigo,
        ]);
    }

    public function courierAssigned(Pedido $order, Repartidor $courier): void
    {
        $this->create([
            'tipo' => Notificacion::TIPO_REPARTIDOR_ASIGNADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_REPARTIDOR,
            'repartidor_id' => $courier->id,
            'pedido_id' => $order->id,
            'titulo' => 'Nuevo pedido asignado',
            'mensaje' => "Tienes asignado el pedido {$order->codigo}.",
        ]);

        $this->push->sendToCourier($courier->id, 'Nuevo pedido asignado', "Tienes asignado el pedido {$order->codigo}.", [
            'url' => route('courier.turno', $courier), 'pedido' => $order->codigo,
        ]);

        $this->create([
            'tipo' => Notificacion::TIPO_REPARTIDOR_ASIGNADO,
            'destinatario_tipo' => Notificacion::DESTINATARIO_ADMIN,
            'pedido_id' => $order->id,
            'titulo' => 'Repartidor asignado',
            'mensaje' => "{$courier->nombreCompleto()} fue asignado al pedido {$order->codigo}.",
        ]);
    }

    private function create(array $attributes): Notificacion
    {
        return Notificacion::query()->create($attributes + [
            'canal' => Notificacion::CANAL_INTERNO,
        ]);
    }
}
