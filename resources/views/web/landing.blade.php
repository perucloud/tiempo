@extends('layouts.web')

@section('title', 'Tiempo Delivery — Comidas y bebidas a tu puerta, a tiempo')
@section('description', 'Pide de los mejores restaurantes de tu zona y nuestros repartidores te lo llevan. Delivery local, puntual de verdad.')

@section('content')

<header>
  <nav class="nav container">
    <a class="logo" href="{{ route('home') }}">
      <span class="clock" aria-hidden="true"></span>
      tiempo<b>delivery</b>
    </a>
    <ul class="nav-links">
      <li><a href="#categorias">Categorías</a></li>
      <li><a href="#como-funciona">Cómo funciona</a></li>
      <li><a href="#restaurantes">Restaurantes</a></li>
      <li><a href="#partner">Registra tu negocio</a></li>
    </ul>
    <a href="{{ route('app.home') }}" class="btn btn-primary nav-cta">Pedir ahora</a>
    <button class="menu-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>
  </nav>
  <div class="mobile-menu" id="mobileMenu">
    <a href="#categorias">Categorías</a>
    <a href="#como-funciona">Cómo funciona</a>
    <a href="#restaurantes">Restaurantes</a>
    <a href="#partner">Registra tu negocio</a>
    <a href="{{ route('app.home') }}" class="btn btn-primary">Pedir ahora</a>
  </div>
</header>

<main id="inicio">

  {{-- HERO --}}
  <section class="hero">
    <div class="hero-swirl" aria-hidden="true"></div>
    <canvas id="heroBubbles" aria-hidden="true"></canvas>

    <div class="container hero-grid">
      <div class="reveal in">
        <span class="eyebrow">⏱ Delivery local, puntual de verdad</span>
        <h1>Tu <span class="word-food">comida</span> y tu <span class="word-drink">bebida</span>,<br><span class="stroke">a tiempo.</span></h1>
        <p class="lead">Pide de los mejores restaurantes de tu zona y nuestros repartidores te lo llevan. Tú eliges, Tiempo lo entrega.</p>
        <div class="hero-actions">
          <a href="{{ route('app.home') }}" class="btn btn-primary">🍽 Quiero pedir</a>
          <a href="#partner" class="btn btn-outline">Tengo un restaurante</a>
        </div>
        <div class="hero-badges">
          <span class="badge">🛵 Flota propia</span>
          <span class="badge">📍 Seguimiento en vivo</span>
          <span class="badge">⚡ Entrega promedio 30 min</span>
        </div>
      </div>

      <div class="hero-visual">
        <img src="{{ asset('images/landing/bolsa.png') }}"     class="hero-img hero-img--bolsa"     alt="" aria-hidden="true">
        <img src="{{ asset('images/landing/chaufa2.png') }}"   class="hero-img hero-img--chaufa"    alt="" aria-hidden="true">
        <img src="{{ asset('images/landing/pollo.png') }}"     class="hero-img hero-img--pollo"     alt="Pollo a la brasa">
        <img src="{{ asset('images/landing/gaseosa.png') }}"   class="hero-img hero-img--delivery"  alt="" aria-hidden="true">
        <img src="{{ asset('images/landing/cerveza.png') }}"   class="hero-img hero-img--cerveza"   alt="" aria-hidden="true">
        <img src="{{ asset('images/landing/sandwichs.png') }}" class="hero-img hero-img--sandwich"  alt="Hamburguesa">

        <div class="float-tag tag-1">🛵 <span>Pedido <b>en camino</b></span></div>
        <div class="float-tag tag-2">⏱ <span>Llega en <b>12 min</b></span></div>

        <img src="{{ asset('images/landing/celular.png') }}" class="hero-img hero-img--celular" alt="App Tiempo Delivery">
      </div>
    </div>
  </section>

  {{-- MARQUEE --}}
  <div class="marquee" aria-hidden="true">
    <div class="marquee-track">
      <span>🍔 COMIDAS &nbsp;•&nbsp; 🥤 BEBIDAS &nbsp;•&nbsp; ⏱ A TIEMPO, SIEMPRE &nbsp;•&nbsp; 🛵 FLOTA PROPIA &nbsp;•&nbsp; 📍 SEGUIMIENTO EN VIVO &nbsp;•&nbsp;</span>
      <span>🍔 COMIDAS &nbsp;•&nbsp; 🥤 BEBIDAS &nbsp;•&nbsp; ⏱ A TIEMPO, SIEMPRE &nbsp;•&nbsp; 🛵 FLOTA PROPIA &nbsp;•&nbsp; 📍 SEGUIMIENTO EN VIVO &nbsp;•&nbsp;</span>
    </div>
  </div>

  {{-- BRAND STRIP: negocios reales desde BD, fallback estático si BD vacía --}}
  @php
  $fallback = [
    ['img'=>asset('images/landing/chaufa.png'),      'color'=>'#2D1F0E', 'name'=>'Chifa Suikao',    'slogan'=>'Comida oriental',    'price'=>'12', 'slug'=>null],
    ['img'=>asset('images/landing/hamburguesa.png'), 'color'=>'#CC3D00', 'name'=>'Burguer Zurdo',   'slogan'=>'Las mejores burgers', 'price'=>'15', 'slug'=>null],
    ['img'=>asset('images/landing/pollo2.png'),      'color'=>'#5E1A7A', 'name'=>'Pollería Rambo',  'slogan'=>'Los mejores pollos',  'price'=>'18', 'slug'=>null],
    ['img'=>asset('images/landing/pilsen.png'),      'color'=>'#0E5C1A', 'name'=>'Cervezas Karlo',  'slogan'=>'Deliciosas bebidas',  'price'=>'10', 'slug'=>null],
    ['img'=>asset('images/landing/chaufa2.png'),     'color'=>'#0A4D6E', 'name'=>'Chifa El Dragón', 'slogan'=>'Sabor auténtico',     'price'=>'13', 'slug'=>null],
    ['img'=>asset('images/landing/sandwichs.png'),   'color'=>'#8B2500', 'name'=>'Sandwich Bros',   'slogan'=>'Siempre frescos',     'price'=>'14', 'slug'=>null],
    ['img'=>asset('images/landing/gaseosa.png'),     'color'=>'#8B0000', 'name'=>'Coca-Cola Perú',  'slogan'=>'La chispa de vida',   'price'=>'5',  'slug'=>null],
  ];
  $stripItems = $businesses->isNotEmpty()
    ? $businesses->map(fn($b) => [
        'img'    => $b->imagen ?? asset('images/landing/chaufa.png'),
        'color'  => $b->colorEfectivo(),
        'name'   => $b->nombre_comercial,
        'slogan' => $b->slogan ?? ucfirst($b->tipo_negocio),
        'price'  => $b->precio_minimo ? number_format($b->precio_minimo, 0) : '—',
        'slug'   => $b->slug,
      ])->all()
    : $fallback;
  $stripItems = array_merge($stripItems, $stripItems);
  @endphp
  <div class="brand-strip">
    <div class="brand-track">
      @foreach($stripItems as $s)
      @if($s['slug'])
        <a href="{{ route('app.negocio.show', $s['slug']) }}" class="rest-slide" style="--img:url('{{ $s['img'] }}'); --color:{{ $s['color'] }}">
      @else
        <div class="rest-slide" style="--img:url('{{ $s['img'] }}'); --color:{{ $s['color'] }}">
      @endif
        <div class="rest-slide-body">
          <span class="rest-slide-time">25-35 min</span>
          <img src="{{ asset('images/landing/moto_ico_white.png') }}" class="rest-slide-moto" alt="">
          <h4>{{ $s['name'] }}</h4>
          <p>{{ $s['slogan'] }}</p>
          <strong>S/ {{ $s['price'] }}<sup>.00</sup></strong>
        </div>
      @if($s['slug'])
        </a>
      @else
        </div>
      @endif
      @endforeach
    </div>
  </div>

  {{-- CATEGORÍAS --}}
  <section class="section" id="categorias">
    <div class="container">
      <div class="section-head center reveal">
        <span class="eyebrow">Categorías</span>
        <h2 class="nowrap">¿Qué se te antoja <span class="text-primary">hoy</span>?</h2>
        <p>Dos mundos para empezar. Elige uno y descubre todos los locales de tu zona.</p>
      </div>

      <div class="cats-grid">
        <a href="{{ route('app.home') }}" class="cat-card cat-food reveal" data-tilt data-glow>
          <span class="cat-emoji" aria-hidden="true">🍔</span>
          <span class="cat-kicker">Categoría 01</span>
          <h3>Comidas</h3>
          <p>Pollerías, chifas, parrillas, menús criollos y más. Toda la carta real de cada restaurante.</p>
          <span class="cat-link">Ver restaurantes →</span>
        </a>

        <a href="{{ route('app.home') }}" class="cat-card cat-drink reveal" data-delay="1" data-tilt data-glow>
          <span class="cat-emoji" aria-hidden="true">🥤</span>
          <span class="cat-kicker">Categoría 02</span>
          <h3>Bebidas</h3>
          <p>Jugos, batidos, gaseosas, cafés y bebidas heladas para acompañar o para el antojo solo.</p>
          <span class="cat-link">Ver locales →</span>
        </a>

        <div class="cat-soon reveal" data-delay="2">
          <b>🚀 Más categorías muy pronto</b>
          <span>Farmacia, mercado, regalos… Tiempo crece contigo.</span>
        </div>
      </div>
    </div>
  </section>

  {{-- CÓMO FUNCIONA --}}
  <section class="section how-bg" id="como-funciona">
    <div class="container">
      <div class="section-head center reveal">
        <span class="eyebrow">Cómo funciona</span>
        <h2>Pedir toma menos de 2 minutos</h2>
      </div>
      <div class="steps">
        <div class="step reveal">
          <div class="step-icon">🗂</div>
          <h3>Elige categoría</h3>
          <p>Comidas o bebidas. Entra y mira todos los locales disponibles en tu zona.</p>
        </div>
        <div class="step reveal" data-delay="1">
          <div class="step-icon">📖</div>
          <h3>Explora la carta</h3>
          <p>Cada restaurante publica su carta real, con precios y fotos. Arma tu pedido a tu gusto.</p>
        </div>
        <div class="step reveal" data-delay="2">
          <div class="step-icon">🛵</div>
          <h3>Tiempo lo recoge</h3>
          <p>El restaurante prepara y entrega a nuestro repartidor. Nosotros hacemos la ruta.</p>
        </div>
        <div class="step reveal" data-delay="3">
          <div class="step-icon">🏠</div>
          <h3>Recíbelo a tiempo</h3>
          <p>Sigue tu pedido en vivo en el mapa y recíbelo en tu puerta, calientito.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- RESTAURANTES --}}
  <section class="section" id="restaurantes">
    <div class="container">
      <div class="slider-head reveal">
        <div class="section-head" style="margin-bottom:0">
          <span class="eyebrow">Aliados</span>
          <h2>Restaurantes que ya están en Tiempo</h2>
        </div>
        <div class="slider-nav">
          <button id="prevBtn" aria-label="Anterior">←</button>
          <button id="nextBtn" aria-label="Siguiente">→</button>
        </div>
      </div>

      <div class="slider" id="slider">
        <a href="{{ route('app.home') }}" class="rest-card">
          <div class="rest-thumb" style="background:linear-gradient(140deg,#FFE1CC,#FFC9A8)">
            <span>🍗</span><span class="rest-tag">⏱ 25–35 min</span>
          </div>
          <div class="rest-body">
            <div class="top"><h3>Pollería El Bracero</h3><span class="rating">★ 4.8</span></div>
            <div class="rest-meta"><span>Pollos y parrillas</span><span>Envío S/ 3</span></div>
          </div>
        </a>
        <a href="{{ route('app.home') }}" class="rest-card">
          <div class="rest-thumb" style="background:linear-gradient(140deg,#FFF0C2,#FFE08F)">
            <span>🥟</span><span class="rest-tag">⏱ 30–40 min</span>
          </div>
          <div class="rest-body">
            <div class="top"><h3>Chifa Fung Wa</h3><span class="rating">★ 4.6</span></div>
            <div class="rest-meta"><span>Chifa</span><span>Envío S/ 3</span></div>
          </div>
        </a>
        <a href="{{ route('app.home') }}" class="rest-card">
          <div class="rest-thumb" style="background:linear-gradient(140deg,#D9F4E7,#B4E8D2)">
            <span>🍛</span><span class="rest-tag">⏱ 20–30 min</span>
          </div>
          <div class="rest-body">
            <div class="top"><h3>Sazón de la Selva</h3><span class="rating">★ 4.9</span></div>
            <div class="rest-meta"><span>Comida regional</span><span>Envío gratis</span></div>
          </div>
        </a>
        <a href="{{ route('app.home') }}" class="rest-card">
          <div class="rest-thumb" style="background:linear-gradient(140deg,#DDF6F3,#B8ECE6)">
            <span>🧃</span><span class="rest-tag">⏱ 15–25 min</span>
          </div>
          <div class="rest-body">
            <div class="top"><h3>Juguería Tropical</h3><span class="rating">★ 4.7</span></div>
            <div class="rest-meta"><span>Jugos y batidos</span><span>Envío S/ 2</span></div>
          </div>
        </a>
        <a href="{{ route('app.home') }}" class="rest-card">
          <div class="rest-thumb" style="background:linear-gradient(140deg,#FFE3E0,#FFC7C2)">
            <span>🍕</span><span class="rest-tag">⏱ 30–40 min</span>
          </div>
          <div class="rest-body">
            <div class="top"><h3>Pizzería La Leña</h3><span class="rating">★ 4.5</span></div>
            <div class="rest-meta"><span>Pizzas al horno</span><span>Envío S/ 3</span></div>
          </div>
        </a>
      </div>
    </div>
  </section>

  {{-- TRACKING --}}
  <section class="section" style="padding-top:0" id="seguimiento">
    <div class="container">
      <div class="track reveal">
        <div>
          <span class="eyebrow" style="background:rgba(255,197,61,.15);color:var(--accent)">📍 Seguimiento en vivo</span>
          <h2>Se llama Tiempo porque cumplimos el tuyo</h2>
          <p>Desde que confirmas el pedido hasta que suena tu timbre, sabes exactamente en qué etapa está y cuánto falta.</p>
          <div class="track-list">
            <div class="track-item">
              <div class="track-dot">🧾</div>
              <div><h4>Estado paso a paso</h4><p>Confirmado, en cocina, en camino, entregado — sin adivinar.</p></div>
            </div>
            <div class="track-item">
              <div class="track-dot">🗺</div>
              <div><h4>Repartidor en el mapa</h4><p>Ves su ubicación real y su ruta hacia tu dirección.</p></div>
            </div>
            <div class="track-item">
              <div class="track-dot">💬</div>
              <div><h4>Contacto directo</h4><p>Escríbele al repartidor o a soporte desde el mismo pedido.</p></div>
            </div>
          </div>
        </div>
        <div class="track-demo">
          <span style="font-size:.8rem;font-weight:800;opacity:.7">TU PEDIDO #TD-1042 LLEGA EN</span>
          <div class="eta-big" id="etaCounter">14:59</div>
          <div class="track-steps">
            <div class="tstep done"><i></i><span>Pedido confirmado — 7:02 pm</span></div>
            <div class="tstep done"><i></i><span>El restaurante está preparando — 7:05 pm</span></div>
            <div class="tstep active"><i></i><span>Repartidor en camino 🛵</span></div>
            <div class="tstep"><i></i><span>Entregado en tu puerta</span></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- PARTNER --}}
  <section class="section" id="partner" style="padding-top:0">
    <div class="container partner">
      <div class="partner-visual reveal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
          <h3 style="font-size:1.1rem">📖 Tu carta digital</h3>
          <span class="eyebrow teal" style="font-size:.72rem">Panel del restaurante</span>
        </div>
        <div class="menu-row">
          <div class="mi">🍗</div>
          <div><h4>1/4 de pollo a la brasa</h4><small>Con papas y ensalada</small></div>
          <span class="price">S/ 18</span>
        </div>
        <div class="menu-row">
          <div class="mi" style="background:var(--accent-soft)">🍟</div>
          <div><h4>Salchipapa especial</h4><small>Doble porción</small></div>
          <span class="price">S/ 12</span>
        </div>
        <div class="menu-row">
          <div class="mi" style="background:var(--drink-soft)">🥤</div>
          <div><h4>Chicha morada 1L</h4><small>Preparada del día</small></div>
          <span class="price">S/ 8</span>
        </div>
        <div style="margin-top:1rem;padding-top:1rem;border-top:1.5px dashed var(--line);display:flex;justify-content:space-between;align-items:center">
          <small style="font-weight:800;color:var(--muted)">La misma carta que ven tus clientes en Tiempo</small>
          <a href="{{ route('admin.login') }}" class="btn btn-teal" style="min-height:auto;padding:.55rem 1.1rem;font-size:.85rem">Acceder al panel</a>
        </div>
      </div>

      <div class="reveal" data-delay="1">
        <span class="eyebrow teal">Para restaurantes</span>
        <h2 style="font-size:clamp(1.8rem,3.6vw + .5rem,2.6rem);margin-top:.9rem">Registra tu restaurante y vende más, sin contratar motorizados</h2>
        <div class="partner-benefits">
          <div class="pb"><span><b>Registro simple:</b> crea tu cuenta, sube tu carta con fotos y precios, y aparece en la app.</span></div>
          <div class="pb"><span><b>Tú cocinas, nosotros repartimos:</b> entregas el pedido a nuestro repartidor y Tiempo se encarga de la ruta.</span></div>
          <div class="pb"><span><b>Tarifa clara de delivery:</b> sin sorpresas — sabes exactamente cuánto cuesta cada envío.</span></div>
          <div class="pb"><span><b>Tu carta, siempre actualizada:</b> cambia precios y platos desde tu panel, en tiempo real.</span></div>
        </div>
        <div style="margin-top:1.8rem;display:flex;gap:.9rem;flex-wrap:wrap">
          <a href="{{ route('admin.login') }}" class="btn btn-teal">Registrar mi restaurante</a>
          <a href="#" class="btn btn-outline">Hablar con un asesor</a>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA FINAL --}}
  <section class="section final">
    <div class="container reveal">
      <h2>El antojo no espera.<br><em>Tiempo tampoco.</em></h2>
      <p>Pide comidas y bebidas de tus locales favoritos y recíbelas cuando dijimos que llegarían.</p>
      <div class="final-actions">
        <a href="{{ route('app.home') }}" class="btn btn-primary">🍔 Pedir comidas</a>
        <a href="{{ route('app.home') }}" class="btn btn-teal">🥤 Pedir bebidas</a>
      </div>
    </div>
  </section>

</main>

<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <a class="logo" href="{{ route('home') }}" style="color:#fff">
          <span class="clock"></span>tiempo<b style="color:var(--accent)">delivery</b>
        </a>
        <p style="font-size:.88rem;margin-top:.9rem;max-width:30ch;font-weight:600">Delivery local con flota propia. Comidas y bebidas hoy; mañana, todo lo que necesites.</p>
      </div>
      <div class="footer-col">
        <h4>Pide</h4>
        <ul>
          <li><a href="{{ route('app.home') }}">Comidas</a></li>
          <li><a href="{{ route('app.home') }}">Bebidas</a></li>
          <li><a href="#seguimiento">Seguimiento</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Negocios</h4>
        <ul>
          <li><a href="#partner">Registra tu restaurante</a></li>
          <li><a href="#">Sé repartidor</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Ayuda</h4>
        <ul>
          <li><a href="#">Preguntas frecuentes</a></li>
          <li><a href="#">Términos</a></li>
          <li><a href="#">Privacidad</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} Tiempo Delivery. Todos los derechos reservados.</span>
      <span>Hecho con ⏱ para llegar siempre a tiempo.</span>
    </div>
  </div>
</footer>

@endsection

@push('web_scripts')
<script>
/* ---------- Bouncy title letters ---------- */
(function(){
    if (matchMedia('(prefers-reduced-motion:reduce)').matches) return;
    const h1 = document.querySelector('.hero h1');
    if (!h1) return;
    let idx = 0;
    function wrap(node) {
        if (node.nodeType === 3) {
            const frag = document.createDocumentFragment();
            [...node.textContent].forEach(ch => {
                if (/\s/.test(ch)) {
                    frag.appendChild(document.createTextNode(ch));
                } else {
                    const s = document.createElement('span');
                    s.className = 'letter';
                    s.style.animationDelay = (idx * 0.075) + 's';
                    s.textContent = ch;
                    frag.appendChild(s);
                    idx++;
                }
            });
            node.parentNode.replaceChild(frag, node);
        } else if (node.nodeType === 1 && node.tagName !== 'BR') {
            [...node.childNodes].forEach(wrap);
        }
    }
    [...h1.childNodes].forEach(wrap);
})();

/* ---------- Hero bubbles ---------- */
(function(){
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const canvas = document.getElementById('heroBubbles');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const COLORS = ['rgba(255,90,31,','rgba(0,179,164,','rgba(255,197,61,','rgba(255,140,80,','rgba(230,200,255,'];
    let bubbles = [], mouse = { x:-9999, y:-9999 }, rafId;

    function resize() {
        const r = canvas.parentElement.getBoundingClientRect();
        canvas.width = r.width; canvas.height = r.height;
    }
    function spawn() {
        bubbles = Array.from({length:100}, () => ({
            x:  Math.random() * canvas.width,
            y:  Math.random() * canvas.height,
            r:  2.5 + Math.random() * 3,
            vx: (Math.random()-.5) * .55,
            vy: (Math.random()-.5) * .55,
            col: COLORS[Math.floor(Math.random()*COLORS.length)],
            a:  0.13 + Math.random() * 0.15,
        }));
    }
    function tick() {
        ctx.clearRect(0,0,canvas.width,canvas.height);
        bubbles.forEach(b => {
            const dx = b.x - mouse.x, dy = b.y - mouse.y;
            const d  = Math.sqrt(dx*dx + dy*dy);
            if (d < 85 && d > 0) {
                const f = (85-d)/85 * 0.9;
                b.vx += dx/d*f; b.vy += dy/d*f;
            }
            b.vx *= 0.97; b.vy *= 0.97;
            const sp = Math.sqrt(b.vx*b.vx+b.vy*b.vy);
            if (sp < 0.08) { b.vx += (Math.random()-.5)*.12; b.vy += (Math.random()-.5)*.12; }
            if (sp > 1.4)  { b.vx = b.vx/sp*1.4; b.vy = b.vy/sp*1.4; }
            b.x += b.vx; b.y += b.vy;
            if (b.x < b.r)              { b.x = b.r;              b.vx = Math.abs(b.vx); }
            if (b.x > canvas.width-b.r) { b.x = canvas.width-b.r; b.vx = -Math.abs(b.vx); }
            if (b.y < b.r)              { b.y = b.r;              b.vy = Math.abs(b.vy); }
            if (b.y > canvas.height-b.r){ b.y = canvas.height-b.r;b.vy = -Math.abs(b.vy); }
            ctx.beginPath();
            ctx.arc(b.x, b.y, b.r, 0, Math.PI*2);
            ctx.fillStyle = b.col + b.a + ')';
            ctx.fill();
        });
        rafId = requestAnimationFrame(tick);
    }

    resize(); spawn(); tick();
    window.addEventListener('resize', () => { resize(); spawn(); });
    const hero = document.querySelector('.hero');
    hero.addEventListener('mousemove', e => {
        const r = canvas.getBoundingClientRect();
        mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top;
    });
    hero.addEventListener('mouseleave', () => { mouse.x=-9999; mouse.y=-9999; });

    /* Pause when hero scrolls out of view */
    new IntersectionObserver(([e]) => e.isIntersecting ? (rafId = requestAnimationFrame(tick)) : cancelAnimationFrame(rafId)).observe(canvas.parentElement);
})();

/* ---------- Reveal on scroll ---------- */
const io = new IntersectionObserver(es => es.forEach(e => {
  if (e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); }
}), {threshold:.14});
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

/* ---------- Mobile menu ---------- */
const toggle = document.querySelector('.menu-toggle');
const menu   = document.getElementById('mobileMenu');
toggle.addEventListener('click', () => {
  const open = menu.classList.toggle('open');
  toggle.setAttribute('aria-expanded', open);
});
menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => menu.classList.remove('open')));

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ---------- 3D tilt + glow ---------- */
if (!reduceMotion && matchMedia('(pointer:fine)').matches){
  document.querySelectorAll('[data-tilt]').forEach(card => {
    card.addEventListener('mousemove', e => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left)/r.width, y = (e.clientY - r.top)/r.height;
      card.style.transform = `rotateY(${(x-.5)*10}deg) rotateX(${(.5-y)*8}deg)`;
      if (card.hasAttribute('data-glow')){
        card.style.setProperty('--mx', (x*100)+'%');
        card.style.setProperty('--my', (y*100)+'%');
      }
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
  });
}

/* ---------- Hero parallax depth ---------- */
if (!reduceMotion && matchMedia('(min-width:900px) and (pointer:fine)').matches) {
    const hero = document.querySelector('.hero');
    const layers = [
        { sel: '.hero-img--cerveza',  depth: 0.015 },
        { sel: '.hero-img--bolsa',    depth: 0.020 },
        { sel: '.hero-img--celular',  depth: 0.025 },
        { sel: '.hero-img--delivery', depth: 0.030 },
        { sel: '.hero-img--chaufa',   depth: 0.030 },
        { sel: '.hero-img--sandwich', depth: 0.035 },
        { sel: '.hero-img--pollo',    depth: 0.042 },
    ].map(l => ({ el: document.querySelector(l.sel), depth: l.depth }))
     .filter(l => l.el);

    if (hero && layers.length) {
        let rafId;
        hero.addEventListener('mousemove', e => {
            cancelAnimationFrame(rafId);
            rafId = requestAnimationFrame(() => {
                const r  = hero.getBoundingClientRect();
                const dx = e.clientX - (r.left + r.width  / 2);
                const dy = e.clientY - (r.top  + r.height / 2);
                layers.forEach(({ el, depth }) => {
                    el.style.setProperty('--px', `${(dx * depth).toFixed(2)}px`);
                    el.style.setProperty('--py', `${(dy * depth).toFixed(2)}px`);
                });
            });
        });
        hero.addEventListener('mouseleave', () => {
            cancelAnimationFrame(rafId);
            layers.forEach(({ el }) => {
                el.style.setProperty('--px', '0px');
                el.style.setProperty('--py', '0px');
            });
        });
    }
}

/* ---------- Slider ---------- */
const slider = document.getElementById('slider');
const cardW  = () => slider.querySelector('.rest-card').offsetWidth + 18;
document.getElementById('prevBtn').onclick = () => slider.scrollBy({left: -cardW(), behavior:'smooth'});
document.getElementById('nextBtn').onclick = () => slider.scrollBy({left:  cardW(), behavior:'smooth'});

/* ---------- ETA countdown ---------- */
let secs = 14*60 + 59;
const eta = document.getElementById('etaCounter');
setInterval(() => {
  if (secs <= 0) return;
  secs--;
  eta.textContent = String(Math.floor(secs/60)).padStart(2,'0') + ':' + String(secs%60).padStart(2,'0');
}, 1000);

</script>
@endpush
