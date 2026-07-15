@extends('layouts.admin')

@section('title', 'Gestionar pedido')
@section('eyebrow', 'Operacion')
@section('page-title', 'Pedido '.$order->codigo)

@section('content')
    <section class="admin-grid admin-grid-3">
        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Resumen</h2>
                    <p>{{ $order->cliente?->nombreCompleto() }} | {{ $order->cliente?->telefono }}</p>
                </div>
            </div>
            <div class="admin-module-list">
                <div class="admin-module-item"><strong>Negocio</strong><span>{{ $order->negocioAfiliado?->nombre_comercial }}</span></div>
                <div class="admin-module-item"><strong>Repartidor</strong><span>{{ $order->repartidor?->nombreCompleto() ?: 'Sin asignar' }}</span></div>
                <div class="admin-module-item"><strong>Direccion</strong><span>{{ $order->direccion_entrega }}</span></div>
                <div class="admin-module-item"><strong>Total</strong><span>S/ {{ number_format((float) $order->total, 2) }}</span></div>
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Productos</h2>
                    <p>Detalle congelado del pedido.</p>
                </div>
            </div>
            <div class="admin-module-list">
                @foreach ($order->detalles as $detail)
                    <div class="admin-module-item">
                        <span>
                            <strong>{{ $detail->producto_nombre }}</strong>
                            <small>{{ $detail->cantidad }} x S/ {{ number_format((float) $detail->precio_unitario, 2) }}</small>
                        </span>
                        <span>S/ {{ number_format((float) $detail->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Estado</h2>
                    <p>Actualiza el avance operativo.</p>
                </div>
            </div>

            @if (session('status'))
                <div class="admin-alert">{{ session('status') }}</div>
            @endif

            <form class="admin-form" method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PUT')
                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        @foreach ($estadoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $order->estado) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="admin-field">
                    <span>Comentario</span>
                    <textarea name="comentario" rows="3">{{ old('comentario') }}</textarea>
                </label>
                <div class="admin-form-actions">
                    <button class="admin-button admin-button-dark" type="submit">Actualizar estado</button>
                </div>
            </form>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Repartidor</h2>
                    <p>Asigna el pedido a un repartidor disponible.</p>
                </div>
            </div>

            <form class="admin-form" method="POST" action="{{ route('admin.orders.courier.update', $order) }}">
                @csrf
                @method('PATCH')
                <label class="admin-field">
                    <span>Repartidor</span>
                    <select name="repartidor_id" required>
                        <option value="">Seleccionar</option>
                        @foreach ($availableCouriers as $courier)
                            <option value="{{ $courier->id }}" @selected((int) old('repartidor_id', $order->repartidor_id) === $courier->id)>
                                {{ $courier->nombreCompleto() }} - {{ $courierEstadoOptions[$courier->estado] ?? $courier->estado }}
                            </option>
                        @endforeach
                    </select>
                    @error('repartidor_id') <small>{{ $message }}</small> @enderror
                </label>
                <label class="admin-field">
                    <span>Comentario</span>
                    <textarea name="comentario" rows="3">{{ old('comentario') }}</textarea>
                    @error('comentario') <small>{{ $message }}</small> @enderror
                </label>
                <div class="admin-form-actions">
                    <button class="admin-button admin-button-dark" type="submit">Asignar repartidor</button>
                </div>
            </form>
        </article>
    </section>

    {{-- Mapa de ubicación del cliente --}}
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2><i class="bi bi-geo-alt"></i> Ubicacion del cliente</h2>
                <p>{{ $order->tieneGeolocalizacion() ? 'Capturada al confirmar el pedido — ' . $order->geolocalizacion_at?->format('d/m/Y H:i') : 'El cliente no compartio su ubicacion.' }}</p>
            </div>
            @if ($order->tieneGeolocalizacion())
                <span class="admin-badge admin-badge-green"><i class="bi bi-check-circle"></i> GPS capturado</span>
            @else
                <span class="admin-badge">Sin GPS</span>
            @endif
        </div>

        @if ($order->tieneGeolocalizacion())
            <div id="mapa-cliente" style="height:320px; border-radius:10px; overflow:hidden;"></div>
            <p style="margin:10px 0 0; font-size:12px; color:var(--admin-muted);">
                <i class="bi bi-pin-map"></i>
                Lat: {{ $order->latitud_cliente }} &nbsp;|&nbsp; Lng: {{ $order->longitud_cliente }}
                &nbsp;&mdash;&nbsp;
                <a class="admin-link" href="https://www.google.com/maps?q={{ $order->latitud_cliente }},{{ $order->longitud_cliente }}" target="_blank" rel="noopener">Ver en Google Maps &nearr;</a>
            </p>
        @else
            <div style="display:flex; align-items:center; justify-content:center; height:120px; border-radius:10px; background:#f8fafc; border:1px dashed var(--admin-line); color:var(--admin-muted); gap:8px;">
                <i class="bi bi-geo-alt" style="font-size:22px;"></i>
                <span>El cliente no activo la geolocalizacion al hacer el pedido.</span>
            </div>
        @endif
    </section>

    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Historial</h2>
                <p>Auditoria de estados del pedido.</p>
            </div>
        </div>
        <div class="admin-module-list">
            @foreach ($order->estados as $state)
                <div class="admin-module-item">
                    <span>
                        <strong>{{ $estadoOptions[$state->estado_nuevo] ?? $state->estado_nuevo }}</strong>
                        <small>{{ $state->comentario ?: 'Sin comentario' }}</small>
                    </span>
                    <span>{{ $state->created_at?->format('d/m/Y H:i') }}</span>
                </div>
            @endforeach
        </div>
    </section>
@if ($order->tieneGeolocalizacion())
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const lat = {{ $order->latitud_cliente }};
            const lng = {{ $order->longitud_cliente }};
            const map = L.map('mapa-cliente', { zoomControl: true, scrollWheelZoom: false }).setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            const icon = L.divIcon({
                html: '<div style="background:#2563eb;width:14px;height:14px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);"></div>',
                className: '', iconAnchor: [7, 7]
            });
            L.marker([lat, lng], { icon })
                .addTo(map)
                .bindPopup('<strong>{{ addslashes($order->cliente?->nombreCompleto() ?? "Cliente") }}</strong><br>{{ addslashes($order->direccion_entrega) }}')
                .openPopup();
        })();
    </script>
    @endpush
@endif

@endsection
