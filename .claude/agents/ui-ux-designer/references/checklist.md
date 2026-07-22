# Checklist de entrega — desktop + móvil

Recorre esta lista antes de entregar cualquier interfaz. Reporta al usuario los puntos verificados y cualquier compromiso consciente que hayas tomado.

## Responsive

- [ ] `<meta name="viewport">` presente.
- [ ] Se ve correcto en 360px, 768px y 1280px de ancho (mínimo estos tres).
- [ ] Sin scroll horizontal de página en ningún ancho.
- [ ] Tablas anchas resueltas (cards, columnas ocultas o scroll interno con indicador).
- [ ] Navegación usable en móvil (hamburguesa, tab bar o equivalente).
- [ ] Modales/menús de desktop tienen su versión móvil (bottom sheet, pantalla completa).
- [ ] Nada depende exclusivamente de `:hover`.
- [ ] Imágenes fluidas (`max-width: 100%`) y con `aspect-ratio` o dimensiones definidas.

## Formularios

- [ ] Todos los inputs tienen borde visible en reposo (`1.5px solid #e2e8f0`).
- [ ] Focus muestra borde brand + sombra suave `0 0 0 3px rgba(37,99,235,.12)` — no solo color de borde.
- [ ] Label real encima de cada campo — ningún campo depende solo del placeholder para identificarse.
- [ ] Estado error: borde rojo + mensaje inline debajo del campo (no solo alert global al tope).
- [ ] Estado disabled visualmente diferenciado (fondo `#f8fafc`, texto `#94a3b8`).
- [ ] En móvil los campos van apilados en columna única aunque el desktop use 2 columnas.

## Cards

- [ ] Cada card usa una variante de paleta pastel del sistema (blue/orange/green/purple/rose/amber).
- [ ] Nivel de sombra coherente con la jerarquía: normal → elevada → hero (no mezclar niveles al azar).
- [ ] Cards clicables tienen hover `translateY(-2px)` + sombra nivel 2 + `cursor: pointer`.
- [ ] Cards informativas sin hover/transform (no confundir al usuario sobre qué es interactivo).
- [ ] Border-radius consistente: 16px standard, 20px hero, 12px compact — no mezclar en la misma vista.

## Modales

- [ ] Overlay con `rgba(15,23,42,.55)` + `backdrop-filter: blur(4px)`.
- [ ] Animación de entrada presente: `scale(.95)→scale(1)` + `opacity 0→1` en 200ms.
- [ ] Estructura header / body / footer respetada. Footer con acciones a la derecha.
- [ ] Max-width `560px` desktop, bottom sheet en móvil.
- [ ] Max-height `85vh` con overflow-y auto en el body — nunca desborda pantalla.
- [ ] Cierre por Esc y clic en overlay implementado.
- [ ] Foco atrapado dentro del modal mientras está abierto.
- [ ] Botón cerrar (×) presente y con área táctil ≥ 44px.
- [ ] Máximo 2 botones en el footer del modal.

## Botones

- [ ] Solo un botón Primary por pantalla o sección.
- [ ] Si hay 3 acciones, la tercera es link de texto, no un tercer botón.
- [ ] Colores de botón respetan el sistema de 6 roles (primary/success/danger/warning/neutral/ghost).
- [ ] Hover muestra sombra en Primary y Success: `0 4px 14px rgba(color,.35)`.
- [ ] Botón ghost tiene borde visible y texto brand — nunca invisible en fondo blanco.

## Badges de estado

- [ ] Se usa la paleta de 8 estados definida (no colores ad-hoc inventados por pantalla).
- [ ] Modo suave para contextos de tabla/lista; modo sólido para el estado principal de un registro.
- [ ] Font-size `12px`, font-weight `600`, border-radius `999px` — forma pill siempre.
- [ ] El punto `::before` solo en estados activos/en curso (Activo, En camino, Pendiente).
- [ ] Texto en modo sólido siempre blanco — verificar contraste ≥ 4.5:1.

## Táctil y formularios

- [ ] Objetivos táctiles ≥ 44×44px con separación entre vecinos.
- [ ] Inputs con `font-size` ≥ 16px (evita zoom automático en iOS).
- [ ] `type`/`inputmode` correctos en cada input (tel, email, numeric…).
- [ ] Cada input tiene `<label>` real asociado, no solo placeholder.
- [ ] Mensajes de error junto al campo, explicando qué corregir.
- [ ] Acción principal alcanzable con el pulgar en móvil.

## Diseño y consistencia

- [ ] Todos los colores y espaciados salen de las variables CSS del plan de tokens — cero valores hex sueltos repetidos.
- [ ] Jerarquía visual clara: se distingue de un vistazo qué es lo más importante de la pantalla.
- [ ] Un solo elemento "firma"; el resto de la interfaz es quieto y disciplinado.
- [ ] Vocabulario consistente: el botón "Publicar" produce un mensaje "Publicado", no "Enviado con éxito".
- [ ] Estados cubiertos: cargando, vacío, error y éxito — no solo el camino feliz.
- [ ] Copy en voz activa, verbos concretos, sin relleno.

## Accesibilidad

- [ ] Contraste texto/fondo ≥ 4.5:1 (3:1 para texto grande y elementos de UI).
- [ ] Foco visible al navegar con Tab, en orden lógico.
- [ ] `prefers-reduced-motion` respetado si hay animaciones.
- [ ] HTML semántico: `<nav>`, `<main>`, `<button>` real (no `<div onclick>`), encabezados en orden (h1 → h2 → h3).
- [ ] Imágenes con `alt` descriptivo (o `alt=""` si son decorativas).

## Rendimiento

- [ ] `loading="lazy"` en imágenes bajo el fold.
- [ ] Sin fuentes web innecesarias (máximo 2 familias, pesos justos).
- [ ] Animaciones solo con `transform` y `opacity` (no `top/left/width`).

## Última mirada

- [ ] Pregúntate: ¿qué adorno puedo quitar sin perder nada? Quítalo.
- [ ] Pregúntate: ¿esta pantalla podría confundirse con una plantilla genérica? Si sí, la firma no está funcionando.
