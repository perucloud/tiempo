# Responsive: desktop + celular

## Breakpoints estándar (mobile-first)

Móvil es el estilo base (sin media query). Las media queries usan `min-width` y agregan complejidad hacia arriba:

| Breakpoint | min-width | Dispositivo típico |
|---|---|---|
| base | — | celular (320–479px) |
| `sm` | 480px | celular grande / landscape |
| `md` | 768px | tablet |
| `lg` | 1024px | laptop |
| `xl` | 1280px | desktop |

```css
/* base = móvil */
.grid-productos { display: grid; grid-template-columns: 1fr; gap: 1rem; }

@media (min-width: 768px) {
  .grid-productos { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1024px) {
  .grid-productos { grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
}
```

Importante: los breakpoints son del contenido, no del dispositivo. Si el diseño se rompe en 900px, agrega un breakpoint en 900px. Prueba estirando la ventana, no solo en los tamaños de la tabla.

## Viewport obligatorio

Sin esto, nada responsive funciona en celular:

```html
<meta name="viewport" content="width=device-width, initial-scale=1">
```

## Patrones de transformación desktop → móvil

Elige el patrón según el componente; no todo se resuelve "apilando":

- **Columnas → pila**: grids multi-columna colapsan a 1 columna. `grid-template-columns: repeat(auto-fit, minmax(280px, 1fr))` lo hace solo, sin media queries.
- **Sidebar → drawer**: menú lateral de desktop se convierte en panel deslizable con botón hamburguesa. En móvil el sidebar fijo roba demasiado ancho.
- **Navbar → menú hamburguesa o tab bar inferior**: para apps de uso frecuente (POS, paneles), la tab bar inferior es superior — los pulgares llegan fácil.
- **Tabla → cards**: las tablas anchas son el punto más doloroso en móvil. Opciones en orden de preferencia: (1) convertir cada fila en una card con pares label/valor, (2) ocultar columnas secundarias, (3) scroll horizontal SOLO en la tabla con `overflow-x: auto` e indicador visual de que hay más contenido — nunca scroll horizontal de página completa.
- **Hover → tap**: en móvil no existe hover. Todo lo que se revela con `:hover` (tooltips, submenús, acciones de fila) necesita alternativa por tap o estar siempre visible.
- **Modal → pantalla completa o bottom sheet**: los modales centrados de desktop en móvil funcionan mejor como bottom sheet o vista completa.

## Reglas táctiles

- Área táctil mínima **44×44px** (48px mejor) para botones, links e íconos. El elemento visual puede ser menor, pero el área clickeable no.
- Separación mínima de 8px entre objetivos táctiles vecinos, para evitar toques accidentales.
- Acciones principales en la mitad inferior de la pantalla (zona del pulgar); acciones destructivas lejos de las frecuentes.
- Inputs con `font-size` mínimo de 16px — menos que eso, iOS hace zoom automático al enfocar.
- Usa el `type` e `inputmode` correctos para invocar el teclado adecuado: `type="tel"`, `type="email"`, `inputmode="numeric"` para montos y DNI.

## Tipografía y espaciado fluidos

```css
h1 { font-size: clamp(1.75rem, 4vw + 1rem, 3rem); }
.seccion { padding-block: clamp(2rem, 6vw, 5rem); }
```

`clamp(mín, preferido, máx)` elimina media queries para tamaños de texto y aire de secciones. Cuerpo de texto: 16px mínimo en móvil, con `line-height` 1.5–1.6 y líneas de máximo ~70 caracteres (`max-width: 65ch`).

## Imágenes y rendimiento móvil

- `max-width: 100%; height: auto;` como regla global de imágenes.
- `srcset`/`sizes` o `<picture>` para servir imágenes chicas a pantallas chicas — en Perú mucha gente navega con datos móviles limitados.
- `loading="lazy"` en imágenes bajo el fold.
- Define `width` y `height` (o `aspect-ratio`) para evitar saltos de layout (CLS).
- Formatos modernos: WebP/AVIF con fallback.

## Errores frecuentes que rompen el responsive

1. Anchos fijos en px en contenedores (`width: 1200px`) — usa `max-width` + `width: 100%`.
2. Elementos posicionados en absoluto que se superponen al encoger la pantalla.
3. Texto dentro de imágenes (ilegible en móvil, invisible para accesibilidad).
4. Olvidar probar formularios largos en móvil con el teclado abierto.
5. `100vh` en móvil: la barra del navegador lo rompe — usa `100dvh` o `min-height` con fallback.
6. Overflow horizontal fantasma: casi siempre causado por un elemento con ancho fijo o un `margin` negativo. Detectar con `* { outline: 1px solid red; }` temporal.
