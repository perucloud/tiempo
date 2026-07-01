<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Support\AdminNavigation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = Notificacion::query()
            ->with(['pedido', 'cliente', 'repartidor'])
            ->when($request->filled('destinatario_tipo'), fn ($query) => $query->where('destinatario_tipo', $request->string('destinatario_tipo')))
            ->when($request->filled('tipo'), fn ($query) => $query->where('tipo', $request->string('tipo')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.notifications.index', [
            'adminModules' => AdminNavigation::for('notificaciones'),
            'notifications' => $notifications,
            'recipientOptions' => [
                Notificacion::DESTINATARIO_ADMIN => 'Admin/Operador',
                Notificacion::DESTINATARIO_CLIENTE => 'Cliente',
                Notificacion::DESTINATARIO_REPARTIDOR => 'Repartidor',
            ],
            'typeOptions' => [
                Notificacion::TIPO_PAGO_APROBADO => 'Pago aprobado',
                Notificacion::TIPO_PAGO_RECHAZADO => 'Pago rechazado',
                Notificacion::TIPO_PEDIDO_ESTADO => 'Estado de pedido',
                Notificacion::TIPO_REPARTIDOR_ASIGNADO => 'Repartidor asignado',
            ],
            'filters' => $request->only(['destinatario_tipo', 'tipo']),
        ]);
    }
}
