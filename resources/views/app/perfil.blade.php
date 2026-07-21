@extends('layouts.app-mobile')

@section('title', 'Mi perfil — TIEMPO')

@section('content')

@php /** @var \App\Models\Cliente $cliente */ @endphp

{{-- Header --}}
<div class="perfil-header">
    <a href="{{ route('app.inicio') }}" class="tracking-back" aria-label="Volver">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div class="perfil-avatar-wrap">
        @if($cliente->foto_perfil)
            <img class="perfil-avatar" src="{{ asset('storage/' . $cliente->foto_perfil) }}" alt="Foto de perfil">
        @else
            <div class="perfil-avatar perfil-avatar-initial">{{ $cliente->iniciales() }}</div>
        @endif
        <form method="POST" action="{{ route('app.perfil.foto') }}" enctype="multipart/form-data" id="foto-form">
            @csrf
            <label class="perfil-avatar-edit" title="Cambiar foto">
                📷
                <input type="file" name="foto" accept="image/*" class="hidden" onchange="document.getElementById('foto-form').submit()">
            </label>
        </form>
    </div>
    <div>
        <h2 class="perfil-name">{{ $cliente->nombreCompleto() }}</h2>
        <span class="perfil-codigo">{{ $cliente->codigo_cliente }}</span>
    </div>
</div>

@if(session('perfil_ok'))
    <div class="tracking-alert tracking-alert-ok">{{ session('perfil_ok') }}</div>
@endif
@if($errors->any())
    <div class="tracking-alert tracking-alert-err">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
@endif

{{-- Tabs --}}
<div class="perfil-tabs" role="tablist">
    <button class="perfil-tab active" data-tab="datos">Mis datos</button>
    <button class="perfil-tab" data-tab="direcciones">Direcciones</button>
    <button class="perfil-tab" data-tab="pedidos">Pedidos</button>
    <button class="perfil-tab" data-tab="preferencias">Preferencias</button>
</div>

{{-- ── TAB: Datos personales ── --}}
<div class="perfil-panel active" id="tab-datos">
    <form class="perfil-form" method="POST" action="{{ route('app.perfil.update') }}">
        @csrf @method('PUT')

        <fieldset class="perfil-section">
            <legend>Información personal</legend>

            <div class="perfil-field">
                <label>Nombre *</label>
                <input type="text" name="nombres" value="{{ old('nombres', $cliente->nombres) }}" required>
            </div>
            <div class="perfil-field">
                <label>Apellidos</label>
                <input type="text" name="apellidos" value="{{ old('apellidos', $cliente->apellidos) }}">
            </div>
            <div class="perfil-field">
                <label>Tipo de documento</label>
                <select name="tipo_documento">
                    <option value="">— Selecciona —</option>
                    @foreach(\App\Models\Cliente::TIPOS_DOCUMENTO as $val => $label)
                        <option value="{{ $val }}" @selected(old('tipo_documento', $cliente->tipo_documento) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="perfil-field">
                <label>Número de documento</label>
                <input type="text" name="documento" value="{{ old('documento', $cliente->documento) }}" maxlength="30">
            </div>
            <div class="perfil-field">
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $cliente->fecha_nacimiento?->format('Y-m-d')) }}">
            </div>
            <div class="perfil-field">
                <label>Sexo</label>
                <select name="sexo">
                    <option value="">— Prefiero no indicar —</option>
                    @foreach(\App\Models\Cliente::SEXOS as $val => $label)
                        <option value="{{ $val }}" @selected(old('sexo', $cliente->sexo) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </fieldset>

        <fieldset class="perfil-section">
            <legend>Datos de contacto</legend>
            <div class="perfil-field">
                <label>Celular</label>
                <input type="tel" value="{{ $cliente->telefono }}" disabled class="input-disabled">
                <small>Tu celular es tu usuario. Para cambiarlo contacta soporte.</small>
            </div>
            <div class="perfil-field">
                <label>WhatsApp (si es distinto)</label>
                <input type="tel" name="whatsapp" value="{{ old('whatsapp', $cliente->whatsapp) }}" maxlength="30">
            </div>
            <div class="perfil-field">
                <label>Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $cliente->email) }}">
            </div>
        </fieldset>

        <button class="auth-btn auth-btn-primary" type="submit">Guardar cambios</button>
    </form>

    {{-- Cambiar contraseña --}}
    <form class="perfil-form" method="POST" action="{{ route('app.perfil.password') }}" style="margin-top:1.5rem">
        @csrf @method('PUT')
        <fieldset class="perfil-section">
            <legend>Cambiar contraseña</legend>
            <div class="perfil-field">
                <label>Contraseña actual</label>
                <input type="password" name="password_actual" autocomplete="current-password">
            </div>
            <div class="perfil-field">
                <label>Nueva contraseña</label>
                <input type="password" name="password" autocomplete="new-password" minlength="8">
            </div>
            <div class="perfil-field">
                <label>Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" autocomplete="new-password">
            </div>
        </fieldset>
        <button class="auth-btn auth-btn-outline" type="submit">Cambiar contraseña</button>
    </form>

    {{-- Logout --}}
    <form method="POST" action="{{ route('app.logout') }}" style="margin-top:1rem">
        @csrf
        <button class="auth-btn auth-btn-ghost" type="submit">Cerrar sesión</button>
    </form>

    {{-- Stats --}}
    <div class="perfil-stats">
        <div class="perfil-stat">
            <span class="perfil-stat-value">{{ $cliente->total_pedidos }}</span>
            <span class="perfil-stat-label">Pedidos</span>
        </div>
        <div class="perfil-stat">
            <span class="perfil-stat-value">S/ {{ number_format($cliente->total_gastado, 2) }}</span>
            <span class="perfil-stat-label">Total gastado</span>
        </div>
        <div class="perfil-stat">
            <span class="perfil-stat-value">{{ $cliente->created_at->diffForHumans() }}</span>
            <span class="perfil-stat-label">Miembro desde</span>
        </div>
    </div>
</div>

{{-- ── TAB: Direcciones ── --}}
<div class="perfil-panel hidden" id="tab-direcciones">
    <div id="direcciones-list" class="direcciones-list">
        @forelse($cliente->direcciones as $dir)
            <div class="dir-card {{ $dir->es_predeterminada ? 'dir-card-default' : '' }}" data-id="{{ $dir->id }}">
                <div class="dir-card-alias">
                    {{ $dir->alias }}
                    @if($dir->es_predeterminada)
                        <span class="dir-badge">Predeterminada</span>
                    @endif
                </div>
                <div class="dir-card-address">{{ $dir->direccion_exacta }}</div>
                @if($dir->referencia)
                    <div class="dir-card-ref">📍 {{ $dir->referencia }}</div>
                @endif
                @if($dir->instrucciones)
                    <div class="dir-card-inst">💬 {{ $dir->instrucciones }}</div>
                @endif
                <div class="dir-card-actions">
                    @unless($dir->es_predeterminada)
                        <button class="dir-btn" onclick="setPredeterminada({{ $dir->id }})">Predeterminar</button>
                    @endunless
                    <button class="dir-btn dir-btn-danger" onclick="deleteDireccion({{ $dir->id }})">Eliminar</button>
                </div>
            </div>
        @empty
            <p class="perfil-empty">No tienes direcciones guardadas aún.</p>
        @endforelse
    </div>

    <button class="auth-btn auth-btn-outline" id="btn-nueva-dir" style="margin-top:1rem">+ Nueva dirección</button>

    <div id="form-nueva-dir" class="dir-form hidden">
        <h3 style="font-size:.95rem;margin:1rem 0 .5rem">Nueva dirección</h3>
        <div class="perfil-field"><label>Alias *</label>
            <input type="text" id="dir-alias" placeholder="Casa, Trabajo, Mamá…" maxlength="50"></div>
        <div class="perfil-field"><label>Dirección exacta *</label>
            <input type="text" id="dir-direccion" placeholder="Calle, número…"></div>
        <div class="perfil-field"><label>Distrito</label>
            <input type="text" id="dir-distrito" placeholder="Satipo"></div>
        <div class="perfil-field"><label>Provincia</label>
            <input type="text" id="dir-provincia" placeholder="Satipo"></div>
        <div class="perfil-field"><label>Región</label>
            <input type="text" id="dir-region" placeholder="Junín"></div>
        <div class="perfil-field"><label>Referencia</label>
            <input type="text" id="dir-ref" placeholder="Frente al parque…"></div>
        <div class="perfil-field"><label>Nombre del receptor</label>
            <input type="text" id="dir-receptor"></div>
        <div class="perfil-field"><label>Celular del receptor</label>
            <input type="tel" id="dir-celular-receptor"></div>
        <label class="auth-check-label" style="margin:.5rem 0">
            <input type="checkbox" id="dir-otra-persona"> ¿Puede recibir otra persona?
        </label>
        <div class="perfil-field"><label>Instrucciones al repartidor</label>
            <input type="text" id="dir-instrucciones" placeholder="La casa verde con portón azul"></div>
        <label class="auth-check-label" style="margin:.5rem 0">
            <input type="checkbox" id="dir-predeterminada"> Establecer como dirección predeterminada
        </label>
        <div class="dir-map-container">
            <label style="font-size:.82rem;font-weight:600">Ubicar en el mapa (opcional)</label>
            <div id="dir-map" class="tracking-map" style="margin-top:.5rem"></div>
            <p class="auth-hint" style="margin-top:.4rem">Toca el mapa para marcar la ubicación exacta.</p>
        </div>
        <input type="hidden" id="dir-latitud">
        <input type="hidden" id="dir-longitud">
        <div style="display:flex;gap:.5rem;margin-top:1rem">
            <button class="auth-btn auth-btn-primary" onclick="saveDireccion()">Guardar</button>
            <button class="auth-btn auth-btn-ghost" onclick="hideDirForm()">Cancelar</button>
        </div>
    </div>
</div>

{{-- ── TAB: Pedidos ── --}}
<div class="perfil-panel hidden" id="tab-pedidos">
    @forelse($pedidos as $pedido)
        <a class="profile-order-item" href="{{ route('app.orders.show', $pedido->codigo) }}">
            <div class="profile-order-main">
                <strong>{{ $pedido->codigo }}</strong>
                <span>{{ $pedido->negocioAfiliado?->nombre_comercial }}</span>
            </div>
            <div class="profile-order-meta">
                <span class="profile-estado">{{ \App\Models\Pedido::ESTADOS_CLIENTE[$pedido->estado] ?? $pedido->estado }}</span>
                <span>S/ {{ number_format($pedido->total, 2) }} · {{ $pedido->created_at->diffForHumans() }}</span>
            </div>
        </a>
    @empty
        <p class="perfil-empty">Aún no tienes pedidos. ¡Haz tu primer pedido!</p>
        <a class="auth-btn auth-btn-primary" href="{{ route('app.inicio') }}" style="margin-top:1rem">Explorar negocios →</a>
    @endforelse
</div>

{{-- ── TAB: Preferencias ── --}}
<div class="perfil-panel hidden" id="tab-preferencias">
    <form class="perfil-form" method="POST" action="{{ route('app.perfil.update') }}">
        @csrf @method('PUT')
        {{-- Re-enviar campos requeridos para no pisar datos --}}
        <input type="hidden" name="nombres" value="{{ $cliente->nombres }}">

        <fieldset class="perfil-section">
            <legend>Notificaciones</legend>
            <label class="perfil-toggle-label">
                <span>Notificaciones Push</span>
                <input type="hidden" name="recibir_push" value="0">
                <input type="checkbox" name="recibir_push" value="1" class="perfil-toggle"
                       @checked($cliente->recibir_push)>
            </label>
            <label class="perfil-toggle-label">
                <span>Correo electrónico</span>
                <input type="hidden" name="recibir_email" value="0">
                <input type="checkbox" name="recibir_email" value="1" class="perfil-toggle"
                       @checked($cliente->recibir_email)>
            </label>
            <label class="perfil-toggle-label">
                <span>WhatsApp</span>
                <input type="hidden" name="recibir_whatsapp" value="0">
                <input type="checkbox" name="recibir_whatsapp" value="1" class="perfil-toggle"
                       @checked($cliente->recibir_whatsapp)>
            </label>
            <label class="perfil-toggle-label">
                <span>Promociones y ofertas</span>
                <input type="hidden" name="recibir_promociones" value="0">
                <input type="checkbox" name="recibir_promociones" value="1" class="perfil-toggle"
                       @checked($cliente->recibir_promociones)>
            </label>
        </fieldset>

        <fieldset class="perfil-section">
            <legend>Método de pago favorito</legend>
            @foreach(\App\Models\Cliente::PREFERENCIAS_PAGO as $val => $label)
                <label class="auth-check-label" style="margin:.4rem 0">
                    <input type="radio" name="preferencia_pago" value="{{ $val }}"
                           @checked($cliente->preferencia_pago === $val)>
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>

        <button class="auth-btn auth-btn-primary" type="submit">Guardar preferencias</button>
    </form>
</div>

@endsection

@push('app_scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
/* ── Tabs ── */
document.querySelectorAll('.perfil-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.perfil-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.perfil-panel').forEach(p => p.classList.add('hidden'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.remove('hidden');
    });
});

/* ── Direcciones AJAX ── */
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
let dirMap = null, dirMarker = null;

document.getElementById('btn-nueva-dir')?.addEventListener('click', () => {
    document.getElementById('form-nueva-dir').classList.remove('hidden');
    document.getElementById('btn-nueva-dir').classList.add('hidden');
    initDirMap();
});

function hideDirForm() {
    document.getElementById('form-nueva-dir').classList.add('hidden');
    document.getElementById('btn-nueva-dir').classList.remove('hidden');
}

function initDirMap() {
    if (dirMap) return;
    const lat = -11.2569, lng = -74.6350; // Satipo centro
    dirMap = L.map('dir-map').setView([lat, lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM', maxZoom: 19 }).addTo(dirMap);
    dirMap.on('click', e => {
        const {lat, lng} = e.latlng;
        if (dirMarker) dirMarker.setLatLng([lat, lng]);
        else dirMarker = L.marker([lat, lng]).addTo(dirMap);
        document.getElementById('dir-latitud').value  = lat;
        document.getElementById('dir-longitud').value = lng;
    });
}

async function saveDireccion() {
    const payload = {
        alias:                      document.getElementById('dir-alias').value,
        direccion_exacta:           document.getElementById('dir-direccion').value,
        distrito:                   document.getElementById('dir-distrito').value,
        provincia:                  document.getElementById('dir-provincia').value,
        region:                     document.getElementById('dir-region').value,
        referencia:                 document.getElementById('dir-ref').value,
        nombre_receptor:            document.getElementById('dir-receptor').value,
        celular_receptor:           document.getElementById('dir-celular-receptor').value,
        puede_recibir_otra_persona: document.getElementById('dir-otra-persona').checked,
        instrucciones:              document.getElementById('dir-instrucciones').value,
        es_predeterminada:          document.getElementById('dir-predeterminada').checked,
        latitud:                    document.getElementById('dir-latitud').value || null,
        longitud:                   document.getElementById('dir-longitud').value || null,
    };

    const res = await fetch('{{ route("app.direcciones.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload),
    });

    if (res.ok) window.location.reload();
    else {
        const json = await res.json();
        alert(json.message ?? 'Error al guardar la dirección.');
    }
}

async function setPredeterminada(id) {
    await fetch(`/app/direcciones/${id}/predeterminada`, {
        method: 'PATCH',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    window.location.reload();
}

async function deleteDireccion(id) {
    if (!confirm('¿Eliminar esta dirección?')) return;
    await fetch(`/app/direcciones/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    window.location.reload();
}
</script>
@endpush
