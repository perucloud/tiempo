@extends('layouts.app-mobile')

@section('title', $negocio->nombre_comercial . ' — TIEMPO Delivery')
@section('description', $negocio->slogan ?? 'Carta de ' . $negocio->nombre_comercial)

@section('content')

{{-- Header del negocio --}}
<div class="carta-header" style="--brand:{{ $negocio->colorEfectivo() }}">
    @if($negocio->imagen)
        <img src="{{ $negocio->imagen }}" alt="{{ $negocio->nombre_comercial }}" class="carta-header-img">
    @endif
    <div class="carta-header-overlay">
        <a href="{{ route('app.home') }}" class="carta-back" aria-label="Volver">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div class="carta-header-body">
            <span class="carta-tipo">{{ ucfirst($negocio->tipo_negocio) }}</span>
            <h1>{{ $negocio->nombre_comercial }}</h1>
            @if($negocio->slogan)
                <p class="carta-slogan">{{ $negocio->slogan }}</p>
            @endif
            <div class="carta-meta">
                <span class="status-badge {{ $negocio->abierto ? '' : 'closed' }}">
                    {{ $negocio->abierto ? '● Abierto' : '● Cerrado' }}
                </span>
                <span>🛵 25-40 min</span>
                @if($negocio->precio_minimo)
                    <span>Desde S/ {{ number_format($negocio->precio_minimo, 2) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Carta / Productos --}}
<section class="content-section">
    <div class="section-heading">
        <h2>Carta</h2>
        <span>{{ $productos->count() }} {{ $productos->count() === 1 ? 'producto' : 'productos' }}</span>
    </div>

    @if(session('cart_status'))
        <p class="cart-status">{{ session('cart_status') }}</p>
    @endif

    <div class="product-list">
        @forelse($productos as $producto)
            <article class="product-card">
                @if($producto->imagen)
                    <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="product-card-img">
                @else
                    <div class="product-card-initial" style="background:{{ $negocio->colorEfectivo() }}">
                        {{ mb_substr($producto->nombre, 0, 1) }}
                    </div>
                @endif
                <div class="product-card-info">
                    <h3>{{ $producto->nombre }}</h3>
                    @if($producto->descripcion)
                        <p>{{ $producto->descripcion }}</p>
                    @endif
                    <strong>{{ $producto->precioVenta() }}</strong>
                </div>
                <form method="POST" action="{{ route('app.cart.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $producto->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" aria-label="Agregar {{ $producto->nombre }}">+</button>
                </form>
            </article>
        @empty
            <div class="carta-empty">
                <span>🍽</span>
                <p>Este negocio aún no tiene productos disponibles.</p>
            </div>
        @endforelse
    </div>
</section>

@endsection
