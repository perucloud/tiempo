@extends('layouts.app-mobile')

@section('title', 'TIEMPO App | Pedidos rápidos')
@section('description', 'App móvil de TIEMPO Delivery para explorar negocios afiliados, comprar y seguir pedidos.')

@section('content')

    {{-- ── Hero ── --}}
    <section class="app-hero">
        <div>
            <p class="app-kicker">Entrega en tu zona</p>
            <h1>¿Qué necesitas ahora?</h1>
        </div>
        <a class="profile-button" href="#perfil" aria-label="Mi perfil">T</a>
    </section>

    {{-- ── Búsqueda ── --}}
    <section id="buscar" class="search-panel" aria-label="Buscar">
        <label for="app-search" class="sr-only">Buscar</label>
        <input id="app-search" type="search" placeholder="Restaurante, producto o categoría…" autocomplete="off">
    </section>

    {{-- Alerta de pedido no encontrado --}}
    @if(session('order_error'))
        <div class="app-alert app-alert-err">{{ session('order_error') }}</div>
    @endif

    {{-- ── Categorías (filtro JS) ── --}}
    <nav class="category-strip" aria-label="Categorías">
        <a href="#negocios" class="cat-all active" data-tipo="">Todos</a>
        @forelse ($categories as $category)
            <a href="#negocios" data-tipo="{{ $category }}">{{ ucfirst($category) }}</a>
        @empty
            <a href="#negocios" data-tipo="restaurante">Restaurantes</a>
        @endforelse
    </nav>

    {{-- ── Negocios ── --}}
    <section id="negocios" class="content-section">
        <div class="section-heading">
            <h2>Negocios afiliados</h2>
            <span id="negocios-count" class="section-count">{{ $businesses->count() }}</span>
        </div>

        <div class="business-list" id="business-list">
            @forelse ($businesses as $business)
                <a class="business-card" href="{{ route('app.negocio.show', $business->slug) }}"
                   data-tipo="{{ $business->tipo_negocio }}"
                   data-search="{{ strtolower($business->nombre_comercial . ' ' . $business->tipo_negocio) }}">
                    <div class="business-visual" style="background:{{ $business->colorEfectivo() }}">
                        <span>{{ mb_substr($business->nombre_comercial, 0, 1) }}</span>
                    </div>
                    <div>
                        <span class="status-badge {{ $business->abierto ? '' : 'closed' }}">
                            {{ $business->abierto ? 'Abierto' : 'Cerrado' }}
                        </span>
                        <h3>{{ $business->nombre_comercial }}</h3>
                        <p>{{ ucfirst($business->tipo_negocio) }}</p>
                        @if($business->tiempo_preparacion)
                            <small>~{{ $business->tiempo_preparacion }} min</small>
                        @else
                            <small>25–40 min</small>
                        @endif
                    </div>
                </a>
            @empty
                <div class="business-empty">
                    <p>Próximamente habrá negocios disponibles en tu zona.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ── Populares ── --}}
    <section class="content-section">
        <div class="section-heading">
            <h2>Populares</h2>
            <a href="#carrito">Carrito</a>
        </div>

        <div class="product-list" id="product-list">
            @forelse ($products as $product)
                <article class="product-card"
                         data-search="{{ strtolower($product->nombre . ' ' . ($product->negocioAfiliado?->nombre_comercial ?? '')) }}">
                    <div>
                        <h3>{{ $product->nombre }}</h3>
                        <p>{{ $product->negocioAfiliado?->nombre_comercial }}</p>
                        <strong>{{ $product->precioVenta() }}</strong>
                    </div>
                    <form method="POST" action="{{ route('app.cart.store') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" aria-label="Agregar {{ $product->nombre }}">+</button>
                    </form>
                </article>
            @empty
                <p class="cart-empty">Cargando productos…</p>
            @endforelse
        </div>
    </section>

    {{-- ── Carrito preview ── --}}
    <section id="carrito" class="cart-preview">
        <div>
            <span>Carrito</span>
            <strong>{{ $cart['count'] }} {{ $cart['count'] === 1 ? 'producto' : 'productos' }}</strong>
        </div>
        <a class="cart-continue" href="#checkout">Ver carrito</a>
    </section>

    {{-- ── Checkout ── --}}
    <section id="checkout" class="cart-detail">
        <div class="section-heading">
            <h2>Tu carrito</h2>
            @if ($cart['business_name'])
                <span>{{ $cart['business_name'] }}</span>
            @endif
        </div>

        @if (session('cart_status'))
            <p class="cart-status">{{ session('cart_status') }}</p>
        @endif

        @error('order')
            <p class="cart-error">{{ $message }}</p>
        @enderror

        @forelse ($cart['items'] as $item)
            <article class="cart-item">
                <div>
                    <strong>{{ $item['product']->nombre }}</strong>
                    <small>{{ $item['product']->precioVenta() }} c/u</small>
                </div>
                <form class="quantity-form" method="POST" action="{{ route('app.cart.update') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" aria-label="Quitar">−</button>
                    <span>{{ $item['quantity'] }}</span>
                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" aria-label="Agregar">+</button>
                </form>
            </article>
        @empty
            <p class="cart-empty">Agrega productos para preparar tu pedido.</p>
        @endforelse

        <form class="delivery-form" method="POST" action="{{ route('app.cart.address') }}">
            @csrf
            @method('PATCH')
            <label for="delivery-address">Dirección de entrega *</label>
            <input id="delivery-address" name="delivery_address" type="text"
                   value="{{ $cart['delivery_address'] }}"
                   placeholder="Calle, número, referencia" required>
            <button type="submit">Guardar dirección</button>
        </form>

        <div class="cart-totals">
            <span>Subtotal <strong>S/ {{ number_format($cart['subtotal'], 2) }}</strong></span>
            <span>Delivery <strong id="delivery-price">Por calcular</strong></span>
            <span>Total <strong id="order-total">S/ {{ number_format($cart['subtotal'], 2) }}</strong></span>
        </div>

        @if ($cart['count'] > 0)
            <form class="delivery-form" method="POST" action="{{ route('app.orders.store') }}" id="order-form">
                @csrf
                <input type="hidden" name="latitud_cliente"  id="geo-lat">
                <input type="hidden" name="longitud_cliente" id="geo-lng">
                {{-- Datos del cliente autenticado (ocultos) --}}
                <input type="hidden" name="nombres"   value="{{ $cliente->nombres }}">
                <input type="hidden" name="telefono"  value="{{ $cliente->telefono }}">
                <input type="hidden" name="email"     value="{{ $cliente->email }}">

                {{-- Resumen del cliente --}}
                <div class="checkout-cliente-info">
                    <div class="checkout-cliente-avatar">{{ $cliente->iniciales() }}</div>
                    <div>
                        <strong>{{ $cliente->nombreCompleto() }}</strong>
                        <span>{{ $cliente->telefono }}</span>
                    </div>
                    <a href="{{ route('app.perfil') }}" class="checkout-edit-link">Editar</a>
                </div>

                <label for="order-notes">Notas para el repartidor (opcional)</label>
                <input id="order-notes" name="notas" type="text"
                       value="{{ old('notas') }}" placeholder="La casa verde, portón azul…">

                <div class="geo-section" id="geo-section">
                    <button class="geo-btn" type="button" id="geo-btn">
                        <span class="geo-icon">📍</span>
                        <span id="geo-label">Compartir ubicación para calcular delivery</span>
                    </button>
                    <small id="geo-hint">Ayuda al repartidor a encontrarte con exactitud.</small>
                    <small id="delivery-quote-status" aria-live="polite"></small>
                </div>

                <button type="submit" class="order-submit-btn" id="order-submit" disabled>Calcula el delivery para continuar</button>
            </form>

            <form method="POST" action="{{ route('app.cart.destroy') }}" style="margin-top:8px">
                @csrf
                @method('DELETE')
                <button class="clear-cart-button" type="submit">Vaciar carrito</button>
            </form>
        @endif
    </section>

    {{-- ── Seguimiento ── --}}
    <section id="pedidos" class="order-status">
        <h2>Seguir pedido</h2>
        <p class="tracking-hint">¿Ya tienes un pedido? Ingresa tu código para ver el estado en tiempo real.</p>
        <form class="tracking-code-form" method="GET" action="" id="track-form">
            <input id="track-input" name="codigo" type="text"
                   placeholder="PED-AAAAMMDD-00001" autocomplete="off"
                   pattern="PED-\d{8}-\d{5}"
                   title="Formato: PED-AAAAMMDD-00001">
            <button type="submit">Ver pedido →</button>
        </form>
    </section>

    {{-- ── Perfil / Historial ── --}}
    <section id="perfil" class="profile-panel">
        <h2>Mis pedidos</h2>
        <p class="profile-hint">Verifica tu teléfono para recuperar tus pedidos de forma segura.</p>

        <form class="profile-search-form" id="profile-form">
            <input id="profile-phone" name="telefono" type="tel"
                   placeholder="9XXXXXXXX" maxlength="15">
            <button type="submit">Enviar código</button>
        </form>

        <form class="profile-search-form hidden" id="profile-otp-form">
            <input id="profile-otp" name="codigo" type="text" inputmode="numeric"
                   placeholder="Código de 6 dígitos" maxlength="6" pattern="[0-9]{6}">
            <button type="submit">Verificar</button>
        </form>

        <p id="profile-message" class="profile-hint" aria-live="polite"></p>
        <button type="button" id="push-enable" class="order-submit-btn">Activar notificaciones</button>

        <div id="profile-results" class="profile-results hidden"></div>

        <a class="profile-back-link" href="{{ route('home') }}">← Volver a la landing</a>
    </section>

@endsection

@push('app_scripts')
<script src="{{ asset('js/geolocalizacion-cliente.js') }}"></script>
<script>
(function () {
    /* ── Búsqueda en tiempo real ── */
    const searchInput = document.getElementById('app-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visibleNegocios = 0;

            document.querySelectorAll('#business-list .business-card').forEach(card => {
                const match = !q || card.dataset.search.includes(q);
                card.style.display = match ? '' : 'none';
                if (match) visibleNegocios++;
            });

            document.querySelectorAll('#product-list .product-card').forEach(card => {
                card.style.display = (!q || card.dataset.search.includes(q)) ? '' : 'none';
            });

            const cnt = document.getElementById('negocios-count');
            if (cnt) cnt.textContent = visibleNegocios;
        });
    }

    /* ── Filtro por categoría ── */
    document.querySelectorAll('.category-strip a').forEach(link => {
        link.addEventListener('click', function () {
            document.querySelectorAll('.category-strip a').forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            const tipo = this.dataset.tipo;
            let visible = 0;

            document.querySelectorAll('#business-list .business-card').forEach(card => {
                const match = !tipo || card.dataset.tipo === tipo;
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            const cnt = document.getElementById('negocios-count');
            if (cnt) cnt.textContent = visible;
        });
    });

    /* ── Formulario de seguimiento ── */
    const trackForm = document.getElementById('track-form');
    if (trackForm) {
        trackForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const codigo = document.getElementById('track-input').value.trim();
            if (codigo) {
                window.location.href = '/app/pedidos/' + encodeURIComponent(codigo);
            }
        });
    }

    /* ── Perfil: acceso OTP e historial ── */
    const profileForm = document.getElementById('profile-form');
    const profileOtpForm = document.getElementById('profile-otp-form');
    const profileMessage = document.getElementById('profile-message');
    const profileResults = document.getElementById('profile-results');
    const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
    })[char]);

    const jsonPost = async (url, payload) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', 'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify(payload),
        });
        const json = await response.json();
        if (!response.ok) throw new Error(json.message || 'No se pudo completar la solicitud.');
        return json;
    };

    const renderOrders = pedidos => {
        profileResults.classList.remove('hidden');
        profileResults.innerHTML = pedidos.length === 0
            ? '<p class="profile-empty">Sin pedidos registrados para ese número.</p>'
            : pedidos.map(p => `
                <a class="profile-order-item" href="${p.url}">
                    <div class="profile-order-main"><strong>${escapeHtml(p.codigo)}</strong><span>${escapeHtml(p.negocio)}</span></div>
                    <div class="profile-order-meta"><span class="profile-estado">${escapeHtml(p.estado)}</span><span>${escapeHtml(p.total)} · ${escapeHtml(p.hace)}</span></div>
                </a>`).join('');
    };

    if (profileForm && profileOtpForm) {
        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const telefono = document.getElementById('profile-phone').value.trim();
            if (!telefono) return;
            profileMessage.textContent = 'Solicitando código...';

            try {
                const json = await jsonPost('{{ route("app.perfil.codigo") }}', { telefono });
                profileOtpForm.classList.remove('hidden');
                profileMessage.textContent = json.debug_code
                    ? `${json.message} Código de prueba: ${json.debug_code}`
                    : json.message;
                document.getElementById('profile-otp').focus();
            } catch (error) {
                profileMessage.textContent = error.message;
            }
        });
    }

    if (profileOtpForm && profileResults) {
        profileOtpForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const telefono = document.getElementById('profile-phone').value.trim();
            const codigo = document.getElementById('profile-otp').value.trim();
            profileMessage.textContent = 'Verificando...';

            try {
                const verification = await jsonPost('{{ route("app.perfil.verificar") }}', { telefono, codigo });
                const history = await jsonPost('{{ route("app.perfil.buscar") }}', { telefono });
                profileMessage.textContent = verification.message;
                renderOrders(history.pedidos || []);
            } catch (error) {
                profileMessage.textContent = error.message;
            }
        });
    }
})();
</script>
@endpush
