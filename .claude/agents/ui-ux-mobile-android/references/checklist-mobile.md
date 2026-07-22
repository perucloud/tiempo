# Checklist final — UI Móvil Android

Recorrer cada punto antes de entregar. Marcar ✅ o indicar qué se ajustó.

## Safe areas y layout base

- [ ] Header tiene `padding-top: env(safe-area-inset-top)` o mínimo 44px para cubrir status bar de Android.
- [ ] Bottom navigation tiene `padding-bottom: env(safe-area-inset-bottom)`.
- [ ] El contenido scrollable tiene `padding-bottom` suficiente para no quedar oculto bajo el bottom nav.
- [ ] No hay contenido importante dentro de las últimas 34px del bottom (gesture navigation de Android).
- [ ] `meta viewport` incluye `viewport-fit=cover` para edge-to-edge en Android 10+.

## Táctil y accesibilidad

- [ ] Todos los elementos interactivos tienen mínimo **44px de alto** (botones, items de lista, tabs, íconos de nav).
- [ ] Targets táctiles con padding suficiente — el tap area es más grande que el ícono visible.
- [ ] Botones destructivos (eliminar, cancelar) están separados del CTA principal — no se puede tocar por error.
- [ ] Ningún link o botón importante está en la zona de alcance difícil (tercio superior de la pantalla, esquina superior izquierda en una mano).
- [ ] Todos los íconos de navegación tienen etiqueta de texto visible o `aria-label`.
- [ ] Inputs tienen `type` correcto: `tel`, `email`, `number`, `search` — el teclado correcto aparece en Android.
- [ ] Inputs tienen `autocomplete` correcto para los autofills nativos de Android.

## Contraste y legibilidad

- [ ] Texto principal sobre `--color-surface`: contraste ≥ 4.5:1.
- [ ] Texto muted / secundario: contraste ≥ 3:1.
- [ ] Texto blanco sobre `--color-brand` en header/botón: contraste ≥ 4.5:1.
- [ ] Tamaño de fuente mínimo: **12px**. Para datos de lectura principal, mínimo **14px**.
- [ ] `font-variant-numeric: tabular-nums` en cantidades numéricas que cambian (balances, precios, contadores).

## Performance móvil

- [ ] Imágenes tienen `loading="lazy"` salvo la primera visible en pantalla.
- [ ] Sin animaciones CSS en `scroll` event — usar `position: sticky` nativo.
- [ ] `transition` máximo `300ms` — por encima se siente lento en Android mid-range.
- [ ] `will-change` solo donde hay animación real — no global.
- [ ] Recursos externos (fonts, íconos) cargados con `preconnect` y `display=swap`.
- [ ] Sin JavaScript que bloquee el render inicial de la pantalla.

## Scroll y overflow

- [ ] Contenedores scrollables tienen `-webkit-overflow-scrolling: touch` (compatibilidad).
- [ ] No hay scroll horizontal accidental en la página — solo en carruseles explícitos.
- [ ] Carruseles horizontales tienen indicador de posición (dots o scroll visible) o se trunca el último ítem para indicar que hay más.
- [ ] `overscroll-behavior: contain` en modales y bottom sheets para no activar el pull-to-refresh de Android.

## Estados y feedback

- [ ] **Loading**: spinner o skeleton visible mientras carga el contenido — nunca pantalla en blanco.
- [ ] **Vacío**: estado vacío con texto y CTA — nunca lista vacía sin explicación.
- [ ] **Error**: mensaje claro en español, sin jerga técnica, con acción para reintentar.
- [ ] **Éxito**: feedback visual inmediato (toast, snackbar, cambio de estado del botón).
- [ ] Botones tienen estado `disabled` y `:active` (presionado) visualmente distintos.
- [ ] Formularios muestran errores inline junto al campo, no solo al enviar.

## Formularios, Cards, Modales y Badges

- [ ] Inputs con borde visible `1.5px` en reposo — no bordes invisibles ni solo underline.
- [ ] Focus muestra borde brand + sombra `0 0 0 3px rgba(brand,.12)`.
- [ ] Label real visible encima de cada campo (no solo placeholder).
- [ ] Cards usan variante pastel del sistema (blue/orange/green/purple/rose/amber) — no colores inventados.
- [ ] Modales en móvil son bottom sheets con handle visible y `overscroll-behavior: contain`.
- [ ] Modal footer tiene máximo 2 botones, acciones a la derecha, fondo `#f8fafc`.
- [ ] Badges usan las 8 variantes del sistema — no colores ad-hoc por pantalla.
- [ ] Modo suave (pastel) en tablas/listas; modo sólido en estado principal del registro.
- [ ] Punto `::before` solo en badges de estado activo/en curso, no en terminales.
- [ ] Sistema de 6 botones respetado: un solo Primary por pantalla, ghost para alternativa sin riesgo.

## Coherencia visual

- [ ] Solo un CTA principal por pantalla — el único con color sólido brand.
- [ ] Máximo 2 pesos de fuente distintos por pantalla (ej: 500 para cuerpo, 700 para títulos).
- [ ] `border-radius` consistente: 16px para cards, 12px para íconos medianos, 8px para badges/chips, 999px para pills.
- [ ] Sombras consistentes: `0 2px 8px rgba(0,0,0,.06)` para cards normales, `0 4px 20px rgba(0,0,0,.1)` para hero.
- [ ] Espaciado en múltiplos de 4px (4, 8, 12, 16, 20, 24, 32, 48).
- [ ] Íconos del mismo set visual — no mezclar estilos (outline vs filled).

## Android específico

- [ ] El color del tema (`meta name="theme-color"`) coincide con el color del header de la app.
- [ ] El manifest PWA tiene `display: standalone` para ocultar la barra del navegador.
- [ ] Back button de Android no rompe la navegación (si hay modales, el back cierra el modal, no sale de la app).
- [ ] El teclado virtual no oculta el campo activo — usar `resize: none` en el `<html>` o `visualViewport` para reposicionar.
- [ ] Texto no se selecciona accidentalmente al hacer long-press en elementos UI (usar `user-select: none` en componentes interactivos, pero nunca en contenido de lectura).

## Consejo final

Antes de entregar, haz el **test del pulgar**: sostén el teléfono con una mano y recorre el flujo completo. Si tienes que cambiar el agarre para llegar a algo importante, el diseño necesita ajuste.
