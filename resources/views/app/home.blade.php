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
        @foreach ($categories as $category)
            <a href="#negocios">{{ $category }}</a>
        @endforeach
    </section>

    <section id="negocios" class="content-section">
        <div class="section-heading">
            <h2>Negocios afiliados</h2>
            <a href="#buscar">Ver todos</a>
        </div>

        <div class="business-list">
            @foreach ($businesses as $business)
                <article class="business-card">
                    <div class="business-visual">
                        <span>{{ mb_substr($business['name'], 0, 1) }}</span>
                    </div>
                    <div>
                        <span class="status-badge">{{ $business['status'] }}</span>
                        <h3>{{ $business['name'] }}</h3>
                        <p>{{ $business['category'] }}</p>
                        <small>{{ $business['eta'] }}</small>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Populares</h2>
            <a href="#carrito">Carrito</a>
        </div>

        <div class="product-list">
            @foreach ($products as $product)
                <article class="product-card">
                    <div>
                        <h3>{{ $product['name'] }}</h3>
                        <p>{{ $product['business'] }}</p>
                        <strong>{{ $product['price'] }}</strong>
                    </div>
                    <button type="button">Agregar</button>
                </article>
            @endforeach
        </div>
    </section>

    <section id="carrito" class="cart-preview">
        <div>
            <span>Carrito</span>
            <strong>0 productos</strong>
        </div>
        <button type="button">Continuar</button>
    </section>

    <section id="pedidos" class="order-status">
        <h2>Seguimiento</h2>
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
