@extends('layouts.admin')

@section('title', 'Configuracion')
@section('eyebrow', 'Parametros operativos')
@section('page-title', 'Configuracion del sistema')

@section('content')
    @if (session('status'))
        <div class="admin-alert">{{ session('status') }}</div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Configuracion general</h2>
                <p>Parametros principales de TIEMPO para contacto, atencion y operacion.</p>
            </div>
        </div>

        <form class="admin-form" method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Nombre del sistema</span>
                    <input type="text" name="settings[nombre_sistema]" value="{{ old('settings.nombre_sistema', $settings['nombre_sistema']->valor) }}" required>
                    @error('settings.nombre_sistema') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Telefono soporte</span>
                    <input type="text" name="settings[telefono_soporte]" value="{{ old('settings.telefono_soporte', $settings['telefono_soporte']->valor) }}">
                    @error('settings.telefono_soporte') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>WhatsApp pedidos</span>
                    <input type="text" name="settings[whatsapp_pedidos]" value="{{ old('settings.whatsapp_pedidos', $settings['whatsapp_pedidos']->valor) }}">
                    @error('settings.whatsapp_pedidos') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Email contacto</span>
                    <input type="email" name="settings[email_contacto]" value="{{ old('settings.email_contacto', $settings['email_contacto']->valor) }}">
                    @error('settings.email_contacto') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Direccion base</span>
                    <input type="text" name="settings[direccion_base]" value="{{ old('settings.direccion_base', $settings['direccion_base']->valor) }}">
                    @error('settings.direccion_base') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Horario atencion</span>
                    <input type="text" name="settings[horario_atencion]" value="{{ old('settings.horario_atencion', $settings['horario_atencion']->valor) }}">
                    @error('settings.horario_atencion') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Tarifa base delivery</span>
                    <input type="number" name="settings[tarifa_base_delivery]" min="0" step="0.10" value="{{ old('settings.tarifa_base_delivery', $settings['tarifa_base_delivery']->valor) }}" required>
                    @error('settings.tarifa_base_delivery') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">Guardar configuracion</button>
            </div>
        </form>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Zonas y tarifas</h2>
                <p>Define cobertura operativa y costos de delivery.</p>
            </div>
            <a class="admin-button admin-button-dark" href="{{ route('admin.delivery-zones.create') }}">Nueva zona</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Cobertura</th>
                        <th>Delivery</th>
                        <th>Pedido minimo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($zones as $zone)
                        <tr>
                            <td>{{ $zone->nombre }}</td>
                            <td>{{ $zone->descripcion_cobertura ?: 'Sin descripcion' }}</td>
                            <td>S/ {{ number_format((float) $zone->costo_delivery, 2) }}</td>
                            <td>{{ $zone->pedido_minimo ? 'S/ '.number_format((float) $zone->pedido_minimo, 2) : 'Sin minimo' }}</td>
                            <td>
                                <span class="admin-badge {{ $zone->activo ? 'admin-badge-green' : 'admin-badge-red' }}">
                                    {{ $zone->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a class="admin-link" href="{{ route('admin.delivery-zones.edit', $zone) }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-link admin-link-danger" type="submit">Desactivar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay zonas de delivery registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $zones->links() }}
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Auditoria reciente</h2>
                <p>Ultimos cambios criticos de configuracion.</p>
            </div>
        </div>

        <div class="admin-module-list">
            @forelse ($audits as $audit)
                <div class="admin-module-item">
                    <span>
                        <strong>{{ $audit->accion }} - {{ $audit->entidad }}</strong>
                        <small>{{ $audit->user?->name ?? 'Sistema' }} | {{ $audit->created_at?->format('d/m/Y H:i') }}</small>
                    </span>
                    <span class="admin-badge">Auditado</span>
                </div>
            @empty
                <div class="admin-module-item">
                    <span>Sin cambios auditados.</span>
                </div>
            @endforelse
        </div>
    </section>
@endsection
