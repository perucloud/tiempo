<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\ConfiguracionAuditoria;
use App\Models\SistemaConfiguracion;
use App\Models\ZonaDelivery;
use App\Services\ConfigurationAuditService;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public const DEFAULT_SETTINGS = [
        'nombre_sistema' => ['grupo' => 'general', 'etiqueta' => 'Nombre del sistema', 'valor' => 'TIEMPO Delivery', 'tipo' => 'string'],
        'telefono_soporte' => ['grupo' => 'contacto', 'etiqueta' => 'Telefono de soporte', 'valor' => '', 'tipo' => 'string'],
        'whatsapp_pedidos' => ['grupo' => 'contacto', 'etiqueta' => 'WhatsApp de pedidos', 'valor' => '', 'tipo' => 'string'],
        'email_contacto' => ['grupo' => 'contacto', 'etiqueta' => 'Email de contacto', 'valor' => '', 'tipo' => 'string'],
        'direccion_base' => ['grupo' => 'operacion', 'etiqueta' => 'Direccion base', 'valor' => '', 'tipo' => 'string'],
        'horario_atencion' => ['grupo' => 'operacion', 'etiqueta' => 'Horario de atencion', 'valor' => '', 'tipo' => 'string'],
        'tarifa_base_delivery' => ['grupo' => 'operacion', 'etiqueta' => 'Tarifa base delivery', 'valor' => '0.00', 'tipo' => 'decimal'],
    ];

    public function index(): View
    {
        $this->ensureDefaults();

        return view('admin.settings.index', [
            'adminModules' => AdminNavigation::for('configuracion'),
            'settings' => SistemaConfiguracion::query()->orderBy('grupo')->orderBy('id')->get()->keyBy('clave'),
            'zones' => ZonaDelivery::query()->latest()->paginate(10),
            'audits' => ConfiguracionAuditoria::query()->with('user')->latest()->limit(10)->get(),
        ]);
    }

    public function update(UpdateSettingsRequest $request, ConfigurationAuditService $audit): RedirectResponse
    {
        $this->ensureDefaults();
        $changes = [];

        foreach ($request->validated('settings') as $key => $value) {
            $setting = SistemaConfiguracion::query()->where('clave', $key)->firstOrFail();
            $previous = $setting->valor;
            $setting->update(['valor' => (string) $value]);

            if ($previous !== (string) $value) {
                $changes[$key] = ['antes' => $previous, 'despues' => (string) $value];
            }
        }

        if ($changes !== []) {
            $audit->record('sistema_configuraciones', null, 'actualizar', $changes);
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Configuracion actualizada correctamente.');
    }

    private function ensureDefaults(): void
    {
        foreach (self::DEFAULT_SETTINGS as $key => $data) {
            SistemaConfiguracion::query()->firstOrCreate(
                ['clave' => $key],
                $data + ['editable' => true],
            );
        }
    }
}
