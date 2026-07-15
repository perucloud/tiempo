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
