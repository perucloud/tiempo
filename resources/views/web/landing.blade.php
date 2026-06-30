@extends('layouts.web')

@section('title', 'TIEMPO Delivery | Delivery local para clientes y negocios afiliados')
@section('description', 'TIEMPO Delivery es una empresa de delivery que opera pedidos, pagos, estados y repartidores para conectar clientes con negocios afiliados.')

@section('content')
    <section class="web-hero">
        <div class="web-hero-content">
            <p class="web-eyebrow">Empresa de delivery, no restaurante</p>
            <h1>TIEMPO Delivery</h1>
            <p>
                Conectamos clientes con restaurantes, cafeterias, bodegas, farmacias y comercios afiliados.
                TIEMPO se encarga de la operacion: pedidos, pagos, estados y reparto.
            </p>
            <div class="web-hero-actions">
                <a class="web-button web-button-primary" href="#clientes">Pedir desde la app</a>
                <a class="web-button web-button-secondary" href="#negocios">Afiliar mi negocio</a>
            </div>
        </div>
    </section>

    <section id="clientes" class="web-section web-section-alt">
        <div class="web-section-inner">
            <div class="web-section-heading">
                <h2>Una app pensada para comprar rapido</h2>
                <p>
                    La experiencia de clientes vivira en `/app`: mobile-first, con carta digital, carrito,
                    comprobantes y seguimiento del pedido.
                </p>
            </div>

            <div class="web-grid web-grid-3">
                <article class="web-card">
                    <strong>Explorar negocios</strong>
                    <p>Clientes encontraran productos de negocios afiliados activos y disponibles.</p>
                </article>
                <article class="web-card">
                    <strong>Comprar y pagar</strong>
                    <p>El flujo contempla carrito, direccion y comprobantes Yape/Plin cuando aplique.</p>
                </article>
                <article class="web-card">
                    <strong>Seguir el pedido</strong>
                    <p>El cliente podra consultar el avance mientras TIEMPO opera la entrega.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="negocios" class="web-section">
        <div class="web-section-inner">
            <div class="web-section-heading">
                <h2>Negocios afiliados con carta digital</h2>
                <p>
                    Los negocios afiliados administran su perfil, productos, categorias, fotos, horarios y promociones.
                    No gestionan pedidos globales, clientes, pagos ni repartidores.
                </p>
            </div>

            <div class="web-grid web-grid-3">
                <article class="web-card">
                    <strong>Restaurantes y cafeterias</strong>
                    <p>Publican carta, precios y disponibilidad para que TIEMPO los ofrezca a clientes.</p>
                </article>
                <article class="web-card">
                    <strong>Bodegas y farmacias</strong>
                    <p>La plataforma esta preparada para comercios afiliados de distintas categorias.</p>
                </article>
                <article class="web-card">
                    <strong>Operacion centralizada</strong>
                    <p>TIEMPO verifica pagos, coordina estados y asigna repartidores.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="operacion" class="web-section web-section-alt">
        <div class="web-section-inner">
            <div class="web-section-heading">
                <h2>Como opera TIEMPO</h2>
                <p>El dashboard `/admin` es la herramienta interna para administradores, operadores y duenos.</p>
            </div>

            <div class="web-grid web-grid-3">
                <div class="web-step">
                    <strong>1. Cliente compra</strong>
                    <p>El pedido nace desde la app movil de clientes.</p>
                </div>
                <div class="web-step">
                    <strong>2. Operador verifica</strong>
                    <p>TIEMPO revisa pago, confirma el pedido y coordina con el negocio afiliado.</p>
                </div>
                <div class="web-step">
                    <strong>3. Repartidor entrega</strong>
                    <p>La entrega avanza por estados hasta cerrar la venta.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="web-section">
        <div class="web-section-inner">
            <div class="web-cta">
                <div>
                    <h2>Una plataforma, tres superficies independientes</h2>
                    <p>
                        Landing publica para captacion, dashboard administrativo para operacion y app movil para clientes.
                    </p>
                </div>
                <div class="web-cta-actions">
                    <a class="web-button web-button-primary" href="#clientes">Ver experiencia cliente</a>
                    <a class="web-button web-button-secondary" href="{{ route('admin.login') }}">Entrar al admin</a>
                </div>
            </div>
        </div>
    </section>
@endsection
