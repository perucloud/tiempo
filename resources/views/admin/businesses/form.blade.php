@extends('layouts.admin')

@section('title', $business->exists ? 'Editar negocio' : 'Nuevo negocio')
@section('eyebrow', 'Red comercial')
@section('page-title', $business->exists ? 'Editar negocio afiliado' : 'Nuevo negocio afiliado')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $business->exists ? 'Actualizar información comercial' : 'Crear negocio afiliado' }}</h2>
                <p>Completa los 5 pasos para registrar toda la información del negocio.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.businesses.index') }}">Volver</a>
        </div>

        {{-- Wizard nav --}}
        <div class="wizard-nav" id="wizardNav">
            <div class="wizard-step active" data-step="1">
                <span class="ws-num">1</span><span>Básicos</span>
            </div>
            <div class="wizard-step" data-step="2">
                <span class="ws-num">2</span><span>Visual</span>
            </div>
            <div class="wizard-step" data-step="3">
                <span class="ws-num">3</span><span>Ubicación</span>
            </div>
            <div class="wizard-step" data-step="4">
                <span class="ws-num">4</span><span>Contacto</span>
            </div>
            <div class="wizard-step" data-step="5">
                <span class="ws-num">5</span><span>Social</span>
            </div>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}" id="wizardForm">
            @csrf
            @if ($method === 'PUT') @method('PUT') @endif

            @if($errors->any())
                <div class="admin-alert admin-alert-error">
                    Por favor corrige los errores marcados en el formulario.
                </div>
            @endif

            {{-- ═══════════════════════════════════════
                 PASO 1 — Datos básicos
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel active" id="step-1">
                <h3 class="wizard-section-title">
                    <i class="bi bi-shop"></i> Datos del negocio
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Nombre comercial *</span>
                        <input type="text" name="nombre_comercial"
                               value="{{ old('nombre_comercial', $business->nombre_comercial) }}"
                               required placeholder="Ej: Pollos El Gordo">
                        @error('nombre_comercial') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Tipo de negocio *</span>
                        <select name="tipo_negocio" required>
                            @foreach ($tipoOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('tipo_negocio', $business->tipo_negocio) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tipo_negocio') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>RUC</span>
                        <input type="text" name="ruc"
                               value="{{ old('ruc', $business->ruc) }}"
                               placeholder="20123456789">
                        @error('ruc') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Estado *</span>
                        <select name="estado" required>
                            @foreach ($estadoOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('estado', $business->estado) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('estado') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Atención ahora *</span>
                        <select name="abierto" required>
                            <option value="1" @selected((string) old('abierto', (int) $business->abierto) === '1')>Abierto</option>
                            <option value="0" @selected((string) old('abierto', (int) $business->abierto) === '0')>Cerrado</option>
                        </select>
                        @error('abierto') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Tiempo de preparación (min)</span>
                        <input type="number" name="tiempo_preparacion" min="0" max="240"
                               value="{{ old('tiempo_preparacion', $business->tiempo_preparacion) }}"
                               placeholder="20">
                        @error('tiempo_preparacion') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Hora apertura</span>
                        <input type="time" name="hora_apertura"
                               value="{{ old('hora_apertura', $business->hora_apertura) }}">
                        @error('hora_apertura') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Hora cierre</span>
                        <input type="time" name="hora_cierre"
                               value="{{ old('hora_cierre', $business->hora_cierre) }}">
                        @error('hora_cierre') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 2 — Identidad visual
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-2">
                <h3 class="wizard-section-title">
                    <i class="bi bi-palette"></i> Identidad visual
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field admin-field-wide">
                        <span>Imagen principal (URL o ruta)</span>
                        <input type="text" name="imagen"
                               value="{{ old('imagen', $business->imagen) }}"
                               placeholder="https://... o /images/negocios/foto.jpg">
                        @error('imagen') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Slogan <small style="font-weight:400;color:#888">(máx. 4 palabras)</small></span>
                        <input type="text" name="slogan"
                               value="{{ old('slogan', $business->slogan) }}"
                               placeholder="Lo mejor del sabor">
                        @error('slogan') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Precio mínimo de pedido (S/)</span>
                        <input type="number" name="precio_minimo" step="0.50" min="0"
                               value="{{ old('precio_minimo', $business->precio_minimo) }}"
                               placeholder="15.00">
                        @error('precio_minimo') <small>{{ $message }}</small> @enderror
                    </label>

                    <div class="admin-field">
                        <span>Color de marca <small style="font-weight:400;color:#888">(hex)</small></span>
                        <div class="wizard-color-row">
                            <input type="color" id="color_marca_picker"
                                   value="{{ old('color_marca', $business->color_marca ?? '#CC3D00') }}">
                            <input type="text" id="color_marca_text" name="color_marca"
                                   value="{{ old('color_marca', $business->color_marca ?? '#CC3D00') }}"
                                   placeholder="#CC3D00">
                        </div>
                        @error('color_marca') <small>{{ $message }}</small> @enderror
                    </div>

                    <label class="admin-field admin-field-wide">
                        <span>Descripción</span>
                        <textarea name="descripcion" rows="4"
                                  placeholder="Describe brevemente el negocio...">{{ old('descripcion', $business->descripcion) }}</textarea>
                        @error('descripcion') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 3 — Ubicación (mapa)
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-3">
                <h3 class="wizard-section-title">
                    <i class="bi bi-geo-alt"></i> Ubicación del negocio
                </h3>

                {{-- Buscador de dirección --}}
                <div class="map-search-row">
                    <input type="text" id="mapSearchInput"
                           placeholder="Buscar dirección... Ej: Av. Larco 500, Miraflores, Lima"
                           autocomplete="off">
                    <button type="button" id="btnMapSearch">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                <div class="map-hint">
                    <i class="bi bi-hand-index-thumb"></i>
                    Haz clic en el mapa para marcar la ubicación exacta del negocio. También puedes arrastrar el marcador.
                </div>

                {{-- Mapa --}}
                <div id="mapa-negocio"></div>

                {{-- Coordenadas capturadas --}}
                <div class="map-coords-row">
                    <label class="admin-field">
                        <span>Latitud</span>
                        <input type="number" id="latitud" name="latitud" step="0.00000001"
                               value="{{ old('latitud', $business->latitud) }}"
                               placeholder="-12.046374">
                        @error('latitud') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="admin-field">
                        <span>Longitud</span>
                        <input type="number" id="longitud" name="longitud" step="0.00000001"
                               value="{{ old('longitud', $business->longitud) }}"
                               placeholder="-77.042793">
                        @error('longitud') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                @if(!$business->latitud)
                    <p class="map-no-coords">
                        <i class="bi bi-info-circle"></i>
                        Sin coordenadas aún — haz clic en el mapa o escribe los valores manualmente.
                    </p>
                @endif

                {{-- Campos de dirección --}}
                <h3 class="wizard-section-title" style="margin-top:1.75rem">
                    <i class="bi bi-signpost"></i> Dirección
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Departamento</span>
                        <input type="text" name="departamento"
                               value="{{ old('departamento', $business->departamento) }}"
                               placeholder="Lima">
                        @error('departamento') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Provincia</span>
                        <input type="text" name="provincia"
                               value="{{ old('provincia', $business->provincia) }}"
                               placeholder="Lima">
                        @error('provincia') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Distrito</span>
                        <input type="text" name="distrito"
                               value="{{ old('distrito', $business->distrito) }}"
                               placeholder="Miraflores">
                        @error('distrito') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Dirección</span>
                        <input type="text" name="direccion"
                               value="{{ old('direccion', $business->direccion) }}"
                               placeholder="Av. Principal 123">
                        @error('direccion') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field admin-field-wide">
                        <span>Referencia</span>
                        <input type="text" name="referencia"
                               value="{{ old('referencia', $business->referencia) }}"
                               placeholder="Al frente del parque, local rojo">
                        @error('referencia') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 4 — Contacto
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-4">
                <h3 class="wizard-section-title">
                    <i class="bi bi-telephone"></i> Información de contacto
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Celular</span>
                        <input type="text" name="celular"
                               value="{{ old('celular', $business->celular) }}"
                               placeholder="987 654 321">
                        @error('celular') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>WhatsApp</span>
                        <input type="text" name="whatsapp"
                               value="{{ old('whatsapp', $business->whatsapp) }}"
                               placeholder="987 654 321">
                        @error('whatsapp') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Teléfono fijo</span>
                        <input type="text" name="telefono_fijo"
                               value="{{ old('telefono_fijo', $business->telefono_fijo) }}"
                               placeholder="01 234 5678">
                        @error('telefono_fijo') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Teléfono principal (historial)</span>
                        <input type="text" name="telefono"
                               value="{{ old('telefono', $business->telefono) }}"
                               placeholder="987 000 000">
                        @error('telefono') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Correo electrónico</span>
                        <input type="email" name="email"
                               value="{{ old('email', $business->email) }}"
                               placeholder="negocio@ejemplo.com">
                        @error('email') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Página web</span>
                        <input type="text" name="pagina_web"
                               value="{{ old('pagina_web', $business->pagina_web) }}"
                               placeholder="https://minegocio.com">
                        @error('pagina_web') <small>{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>

            {{-- ═══════════════════════════════════════
                 PASO 5 — Redes sociales
            ═══════════════════════════════════════════ --}}
            <div class="wizard-panel" id="step-5">
                <h3 class="wizard-section-title">
                    <i class="bi bi-share"></i> Redes sociales
                </h3>
                <div class="admin-form-grid">
                    <label class="admin-field">
                        <span>Facebook</span>
                        <input type="text" name="facebook"
                               value="{{ old('facebook', $business->facebook) }}"
                               placeholder="https://facebook.com/minegocio">
                        @error('facebook') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>Instagram</span>
                        <input type="text" name="instagram"
                               value="{{ old('instagram', $business->instagram) }}"
                               placeholder="https://instagram.com/minegocio">
                        @error('instagram') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="admin-field">
                        <span>TikTok</span>
                        <input type="text" name="tiktok"
                               value="{{ old('tiktok', $business->tiktok) }}"
                               placeholder="https://tiktok.com/@minegocio">
                        @error('tiktok') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="admin-alert" style="margin-top:1.5rem;background:#eff6ff;border-color:#bfdbfe;color:#1e40af">
                    <strong>¡Todo listo!</strong>
                    Revisa la información y guarda el negocio.
                    Los campos vacíos se pueden completar más adelante desde <em>Editar</em>.
                </div>
            </div>

            {{-- Wizard footer --}}
            <div class="wizard-footer">
                <button type="button" class="admin-button admin-button-logout" id="btn-prev">
                    ← Anterior
                </button>
                <div class="wizard-footer-right">
                    <button type="button" class="admin-button admin-button-primary" id="btn-next">
                        Siguiente →
                    </button>
                    <button type="submit" class="admin-button admin-button-dark wizard-hidden" id="btn-submit">
                        <i class="bi bi-check-lg"></i>
                        {{ $business->exists ? 'Guardar cambios' : 'Crear negocio' }}
                    </button>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WPeE=" crossorigin=""></script>
<script>
(function () {
    /* ─── Wizard navigation ──────────────────────────────── */
    const TOTAL   = 5;
    let current   = 1;

    const wSteps  = document.querySelectorAll('.wizard-step');
    const wPanels = document.querySelectorAll('.wizard-panel');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnSub  = document.getElementById('btn-submit');

    function show(n) {
        n = Math.max(1, Math.min(TOTAL, n));
        wSteps.forEach((s, i) => {
            s.classList.toggle('active', i + 1 === n);
            s.classList.toggle('done',   i + 1 < n);
        });
        wPanels.forEach((p, i) => p.classList.toggle('active', i + 1 === n));
        btnPrev.style.visibility = n === 1 ? 'hidden' : 'visible';
        btnNext.classList.toggle('wizard-hidden', n === TOTAL);
        btnSub.classList.toggle('wizard-hidden',  n !== TOTAL);
        current = n;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (n === 3) setTimeout(initMap, 80);
    }

    btnNext.addEventListener('click', () => show(current + 1));
    btnPrev.addEventListener('click', () => show(current - 1));
    wSteps.forEach((s, i) => s.addEventListener('click', () => show(i + 1)));

    /* ─── Auto-jump al paso con errores ─────────────────── */
    const stepFields = {
        1: ['nombre_comercial','tipo_negocio','ruc','estado','abierto','hora_apertura','hora_cierre','tiempo_preparacion'],
        2: ['imagen','slogan','precio_minimo','color_marca','descripcion'],
        3: ['departamento','provincia','distrito','direccion','referencia','latitud','longitud'],
        4: ['celular','whatsapp','telefono_fijo','telefono','email','pagina_web'],
        5: ['facebook','instagram','tiktok'],
    };
    let jumpTo = null;
    document.querySelectorAll('[name]').forEach(el => {
        if (!el.closest('.admin-field')?.querySelector('small')) return;
        for (const [step, fields] of Object.entries(stepFields)) {
            if (fields.includes(el.name)) {
                const s = parseInt(step);
                if (!jumpTo || s < jumpTo) jumpTo = s;
                break;
            }
        }
    });
    show(jumpTo || 1);

    /* ─── Color picker sync ──────────────────────────────── */
    const picker = document.getElementById('color_marca_picker');
    const cText  = document.getElementById('color_marca_text');
    if (picker && cText) {
        picker.addEventListener('input', () => { cText.value = picker.value; });
        cText.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(cText.value)) picker.value = cText.value;
        });
    }

    /* ─── Mapa Leaflet ───────────────────────────────────── */
    let mapObj    = null;
    let mapMarker = null;
    let mapReady  = false;

    // Valores iniciales (editing con coords existentes)
    const initLat = parseFloat(document.getElementById('latitud')?.value);
    const initLng = parseFloat(document.getElementById('longitud')?.value);

    function initMap() {
        if (mapReady) { mapObj.invalidateSize(); return; }

        const center  = (!isNaN(initLat) && !isNaN(initLng)) ? [initLat, initLng] : [-9.19, -75.01];
        const zoom    = (!isNaN(initLat) && !isNaN(initLng)) ? 16 : 6;

        mapObj = L.map('mapa-negocio').setView(center, zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(mapObj);

        // Marcador inicial si hay coords
        if (!isNaN(initLat) && !isNaN(initLng)) {
            placeMarker(initLat, initLng);
        }

        // Click en mapa → colocar/mover marcador
        mapObj.on('click', e => placeMarker(e.latlng.lat, e.latlng.lng));

        mapReady = true;
    }

    function placeMarker(lat, lng) {
        if (mapMarker) {
            mapMarker.setLatLng([lat, lng]);
        } else {
            mapMarker = L.marker([lat, lng], { draggable: true }).addTo(mapObj);
            mapMarker.on('dragend', () => {
                const pos = mapMarker.getLatLng();
                fillCoords(pos.lat, pos.lng);
            });
        }
        fillCoords(lat, lng);
    }

    function fillCoords(lat, lng) {
        document.getElementById('latitud').value  = lat.toFixed(8);
        document.getElementById('longitud').value = lng.toFixed(8);
    }

    // Inputs manuales → mover marcador en el mapa
    ['latitud', 'longitud'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => {
            const lat = parseFloat(document.getElementById('latitud').value);
            const lng = parseFloat(document.getElementById('longitud').value);
            if (!isNaN(lat) && !isNaN(lng) && mapReady) {
                placeMarker(lat, lng);
                mapObj.setView([lat, lng], 16);
            }
        });
    });

    /* ─── Búsqueda de dirección (Nominatim) ─────────────── */
    async function searchAddress(query) {
        if (!query.trim()) return;
        const btn = document.getElementById('btnMapSearch');
        btn.disabled = true;
        btn.textContent = 'Buscando…';
        try {
            const url = 'https://nominatim.openstreetmap.org/search?'
                + new URLSearchParams({ q: query, format: 'json', limit: 1, countrycodes: 'pe' });
            const res  = await fetch(url, { headers: { 'Accept-Language': 'es' } });
            const data = await res.json();
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                if (!mapReady) initMap();
                placeMarker(lat, lng);
                mapObj.setView([lat, lng], 17);
            } else {
                alert('No se encontró esa dirección. Intenta ser más específico.');
            }
        } catch {
            alert('Error al buscar. Verifica tu conexión a internet.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-search"></i> Buscar';
        }
    }

    document.getElementById('btnMapSearch')?.addEventListener('click', () => {
        searchAddress(document.getElementById('mapSearchInput').value);
    });

    document.getElementById('mapSearchInput')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); searchAddress(e.target.value); }
    });

})();
</script>
@endpush
