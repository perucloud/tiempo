<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDeliveryZoneRequest;
use App\Http\Requests\Admin\UpdateDeliveryZoneRequest;
use App\Models\ZonaDelivery;
use App\Services\ConfigurationAuditService;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeliveryZoneController extends Controller
{
    public function create(): View
    {
        return view('admin.settings.zone-form', [
            'adminModules' => AdminNavigation::for('configuracion'),
            'zone' => new ZonaDelivery(['activo' => true]),
            'action' => route('admin.delivery-zones.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreDeliveryZoneRequest $request, ConfigurationAuditService $audit): RedirectResponse
    {
        $data = $request->validated();
        $data['activo'] = $request->boolean('activo');
        $zone = ZonaDelivery::query()->create($data);

        $audit->record('zonas_delivery', $zone->id, 'crear', $zone->only([
            'nombre',
            'descripcion_cobertura',
            'costo_delivery',
            'pedido_minimo',
            'activo',
        ]));

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Zona de delivery creada correctamente.');
    }

    public function edit(ZonaDelivery $deliveryZone): View
    {
        return view('admin.settings.zone-form', [
            'adminModules' => AdminNavigation::for('configuracion'),
            'zone' => $deliveryZone,
            'action' => route('admin.delivery-zones.update', $deliveryZone),
            'method' => 'PUT',
        ]);
    }

    public function update(UpdateDeliveryZoneRequest $request, ZonaDelivery $deliveryZone, ConfigurationAuditService $audit): RedirectResponse
    {
        $data = $request->validated();
        $data['activo'] = $request->boolean('activo');
        $before = $deliveryZone->only(array_keys($data));
        $deliveryZone->update($data);

        $audit->record('zonas_delivery', $deliveryZone->id, 'actualizar', [
            'antes' => $before,
            'despues' => $deliveryZone->only(array_keys($data)),
        ]);

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Zona de delivery actualizada correctamente.');
    }

    public function destroy(ZonaDelivery $deliveryZone, ConfigurationAuditService $audit): RedirectResponse
    {
        $audit->record('zonas_delivery', $deliveryZone->id, 'eliminar', $deliveryZone->only([
            'nombre',
            'costo_delivery',
            'pedido_minimo',
            'activo',
        ]));
        $deliveryZone->delete();

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Zona de delivery desactivada correctamente.');
    }
}
