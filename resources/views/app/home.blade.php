@extends('layouts.app-mobile')

@section('title', 'TIEMPO App | Pedidos rapidos')
@section('description', 'App movil de TIEMPO Delivery para explorar negocios afiliados, comprar y seguir pedidos.')

@section('content')
    <section class="app-hero">
        <div>
            <p class="app-kicker">Entrega en tu zona</p>
            <h1>Que necesitas ahora?</h1>
        </div>
        <a class="profile-button" href="#perfil" aria-label="Abrir perfil">T</a>
    </section>

    <section id="buscar" class="search-panel" aria-label="Buscar productos o negocios">
        <label for="app-search">Buscar</label>
        <input id="app-search" type="search" placeholder="Restaurante, producto o categoria">
    </section>

    <section class="category-strip" aria-label="Categorias">
        @forelse ($categories as $category)
            <a href="#negocios">{{ $category }}</a>
        @empty
            <a href="#negocios">Comidas</a>
            <a href="#negocios">Bebidas</a>
        @endforelse
    </section>

    <section id="negocios" class="content-section">
        <div class="section-heading">
            <h2>Negocios afiliados</h2>
            <a href="#buscar">Ver todos</a>
        </div>

        <div class="business-list">
            @forelse ($businesses as $business)
                <article class="business-card">
                    <div class="business-visual">
                        <span>{{ mb_substr($business->nombre_comercial, 0, 1) }}</span>
                    </div>
                    <div>
                        <span class="status-badge">{{ $business->abierto ? 'Abierto' : 'Cerrado' }}</span>
                        <h3>{{ $business->nombre_comercial }}</h3>
                        <p>{{ ucfirst($business->tipo_negocio) }} afiliado</p>
                        <small>25-40 min</small>
                    </div>
                </article>
            @empty
                <article class="business-card">
                    <div class="business-visual"><span>T</span></div>
                    <div>
                        <span class="status-badge">Pronto</span>
                        <h3>Negocios en preparacion</h3>
                        <p>TIEMPO esta afiliando comercios para tu zona.</p>
                        <small>Catalogo inicial</small>
                    </div>
                </article>
            @endforelse
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Populares</h2>
            <a href="#carrito">Carrito</a>
        </div>

        <div class="product-list">
            @forelse ($products as $product)
                <article class="product-card">
                    <div>
                        <h3>{{ $product->nombre }}</h3>
                        <p>{{ $product->negocioAfiliado?->nombre_comercial }}</p>
                        <strong>{{ $product->precioVenta() }}</strong>
                    </div>
                    <form method="POST" action="{{ route('app.cart.store') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit">Agregar</button>
                    </form>
                </article>
            @empty
                <article class="product-card">
                    <div>
                        <h3>Catalogo en preparacion</h3>
                        <p>Los productos activos apareceran aqui.</p>
                        <strong>S/ 0.00</strong>
                    </div>
                    <button type="button" disabled>Pronto</button>
                </article>
            @endforelse
        </div>
    </section>

    <section id="carrito" class="cart-preview">
        <div>
            <span>Carrito</span>
            <strong>{{ $cart['count'] }} {{ $cart['count'] === 1 ? 'producto' : 'productos' }}</strong>
        </div>
        <a class="cart-continue" href="#checkout">Continuar</a>
    </section>

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

        @if (session('order_status'))
            <p class="cart-status">{{ session('order_status') }}</p>
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
                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" aria-label="Quitar uno">-</button>
                    <span>{{ $item['quantity'] }}</span>
                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" aria-label="Agregar uno">+</button>
                </form>
            </article>
        @empty
            <p class="cart-empty">Agrega productos para preparar tu pedido.</p>
        @endforelse

        <form class="delivery-form" method="POST" action="{{ route('app.cart.address') }}">
            @csrf
            @method('PATCH')
            <label for="delivery-address">Direccion de entrega</label>
            <input id="delivery-address" name="delivery_address" type="text" value="{{ $cart['delivery_address'] }}" placeholder="Calle, numero, referencia">
            <button type="submit">Guardar direccion</button>
        </form>

        <div class="cart-totals">
            <span>Subtotal <strong>S/ {{ number_format($cart['subtotal'], 2) }}</strong></span>
            <span>Delivery <strong>S/ {{ number_format($cart['delivery'], 2) }}</strong></span>
            <span>Total <strong>S/ {{ number_format($cart['total'], 2) }}</strong></span>
        </div>

        @if ($cart['count'] > 0)
            <form class="delivery-form" method="POST" action="{{ route('app.orders.store') }}">
                @csrf
                <label for="customer-names">Nombres</label>
                <input id="customer-names" name="nombres" type="text" value="{{ old('nombres') }}" placeholder="Tu nombre" required>

                <label for="customer-phone">Telefono</label>
                <input id="customer-phone" name="telefono" type="text" value="{{ old('telefono') }}" placeholder="Numero de contacto" required>

                <label for="customer-email">Email opcional</label>
                <input id="customer-email" name="email" type="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com">

                <label for="order-notes">Notas opcionales</label>
                <input id="order-notes" name="notas" type="text" value="{{ old('notas') }}" placeholder="Referencia o indicaciones">

                <button type="submit">Crear pedido</button>
            </form>
        @endif

        @if ($cart['count'] > 0)
            <form method="POST" action="{{ route('app.cart.destroy') }}">
                @csrf
                @method('DELETE')
                <button class="clear-cart-button" type="submit">Vaciar carrito</button>
            </form>
        @endif
    </section>

    <section id="pedidos" class="order-status">
        <h2>Seguimiento</h2>
        <form class="delivery-form payment-form" method="POST" action="{{ route('app.payments.store') }}">
            @csrf
            <label for="payment-code">Codigo de pedido</label>
            <input id="payment-code" name="codigo" type="text" value="{{ old('codigo') }}" placeholder="PED-YYYYMMDD-00001" required>

            <label for="payment-method">Metodo de pago</label>
            <select id="payment-method" name="metodo" required>
                <option value="yape">Yape</option>
                <option value="plin">Plin</option>
            </select>

            <label for="payment-operation">Codigo de operacion</label>
            <input id="payment-operation" name="codigo_operacion" type="text" value="{{ old('codigo_operacion') }}" placeholder="Operacion o referencia">

            <label for="payment-voucher">Voucher URL</label>
            <input id="payment-voucher" name="voucher_path" type="url" value="{{ old('voucher_path') }}" placeholder="https://...">

            <button type="submit">Enviar pago</button>
        </form>

        <ol>
            <li class="is-current">Pendiente de pago</li>
            <li>Pago en revision</li>
            <li>Pedido aprobado</li>
            <li>En camino</li>
        </ol>
    </section>

    <section id="perfil" class="profile-panel">
        <h2>Perfil cliente</h2>
        <p>Inicia sesion para guardar direcciones, consultar historial y seguir tus pedidos.</p>
        <a href="{{ route('home') }}">Volver a la landing</a>
    </section>
@endsection
