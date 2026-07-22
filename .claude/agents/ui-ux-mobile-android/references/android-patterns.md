# Patrones nativos Android — Referencia de implementación

## Navigation patterns

### Bottom Navigation (≤ 5 destinos)
```css
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 56px;
    padding-bottom: env(safe-area-inset-bottom);
    background: var(--color-surface);
    border-top: 1px solid var(--color-border);
    display: flex;
    align-items: center;
    z-index: 100;
}
.bottom-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    min-height: 44px;
    color: var(--color-muted);
    font-size: 11px;
    font-weight: 500;
    text-decoration: none;
    transition: color .2s;
}
.bottom-nav-item.is-active {
    color: var(--color-brand);
}
/* Indicador activo: punto pequeño debajo del ícono */
.bottom-nav-item.is-active::after {
    content: '';
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--color-brand);
    position: absolute;
    bottom: calc(env(safe-area-inset-bottom) + 6px);
}
```

### Header / App Bar
```css
.app-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: var(--color-brand);
    color: #fff;
    padding: calc(env(safe-area-inset-top) + 12px) 16px 16px;
    min-height: 56px;
    display: flex;
    align-items: center;
    gap: 12px;
}
/* Header hero (con dato grande) */
.app-header--hero {
    padding-bottom: 28px;
    flex-direction: column;
    align-items: flex-start;
}
.hero-label  { font-size: 13px; opacity: .8; margin-bottom: 4px; }
.hero-amount { font-size: 36px; font-weight: 800; font-variant-numeric: tabular-nums; }
```

### Bottom Sheet
```css
.bottom-sheet-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 200;
    display: flex; align-items: flex-end;
}
.bottom-sheet {
    background: var(--color-surface);
    border-radius: 20px 20px 0 0;
    padding: 8px 16px calc(env(safe-area-inset-bottom) + 16px);
    width: 100%;
    max-height: 85dvh;
    overflow-y: auto;
}
.bottom-sheet-handle {
    width: 36px; height: 4px;
    background: var(--color-border);
    border-radius: 2px;
    margin: 0 auto 16px;
}
```

## Cards

### Card estándar
```css
.card {
    background: var(--color-surface);
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.card--hero {
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,.1);
}
.card--outlined {
    box-shadow: none;
    border: 1px solid var(--color-border);
}
```

### Card de balance / dato financiero
```html
<div class="card card--hero">
    <p class="card-label">Mi billetera</p>
    <p class="card-amount">S/ 100.00</p>
    <button class="card-action">Recargar →</button>
</div>
```
```css
.card-label  { font-size: 13px; color: var(--color-muted); margin-bottom: 6px; }
.card-amount { font-size: 32px; font-weight: 800; font-variant-numeric: tabular-nums; }
.card-action { font-size: 14px; color: var(--color-brand); font-weight: 600; margin-top: 12px; }
```

## Listas

### Lista de transacciones (patrón más común)
```html
<ul class="tx-list">
    <li class="tx-item">
        <span class="tx-icon" style="background: #e8f5e9">🛒</span>
        <div class="tx-info">
            <strong>Supermercados</strong>
            <span>17:00 · April 24</span>
        </div>
        <span class="tx-amount tx-amount--negative">-S/ 140</span>
    </li>
</ul>
```
```css
.tx-list { list-style: none; padding: 0; }
.tx-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid var(--color-border);
    min-height: 56px;
}
.tx-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.tx-info { flex: 1; }
.tx-info strong { display: block; font-size: 15px; font-weight: 500; }
.tx-info span   { font-size: 12px; color: var(--color-muted); }
.tx-amount { font-size: 15px; font-weight: 600; font-variant-numeric: tabular-nums; }
.tx-amount--positive { color: var(--color-positive); }
.tx-amount--negative { color: var(--color-negative); }
```

### Lista agrupada por fecha
```html
<section>
    <h3 class="list-group-label">Hoy</h3>
    <ul class="tx-list">...</ul>
</section>
<section>
    <h3 class="list-group-label">Ayer</h3>
    <ul class="tx-list">...</ul>
</section>
```
```css
.list-group-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--color-muted);
    padding: 16px 0 8px;
}
```

## Grid de categorías

```html
<div class="category-grid">
    <a class="category-tile" href="#">
        <span class="category-icon">🍔</span>
        <span>Comida</span>
    </a>
    <!-- ... -->
</div>
```
```css
.category-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.category-tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 8px;
    background: var(--color-surface);
    border-radius: 16px;
    border: 1px solid var(--color-border);
    text-decoration: none;
    color: var(--color-text);
    font-size: 13px;
    font-weight: 500;
    text-align: center;
    min-height: 44px;
    transition: background .15s;
}
.category-tile:active { background: var(--color-bg); }
.category-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    background: var(--color-brand-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
}
```

## Quick actions (accesos rápidos en fila)

```css
.quick-actions {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: none;
}
.quick-actions::-webkit-scrollbar { display: none; }
.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    min-width: 72px;
    font-size: 12px;
    color: var(--color-text);
    text-align: center;
    flex-shrink: 0;
}
.quick-action-icon {
    width: 52px; height: 52px;
    border-radius: 16px;
    background: var(--color-brand-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
}
```

## Tabs (filtro diario/semanal/mensual)

```css
.tabs {
    display: flex;
    background: var(--color-bg);
    border-radius: 10px;
    padding: 3px;
    gap: 2px;
}
.tab {
    flex: 1;
    padding: 8px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-align: center;
    color: var(--color-muted);
    cursor: pointer;
    transition: all .2s;
    min-height: 44px;
    display: flex; align-items: center; justify-content: center;
}
.tab.is-active {
    background: var(--color-brand);
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
```

## Chips / Tags

```css
.chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid currentColor;
}
.chip--brand  { color: var(--color-brand); background: var(--color-brand-light); border-color: transparent; }
.chip--muted  { color: var(--color-muted); background: var(--color-bg); }
```

## Snackbar / Toast

```css
.snackbar {
    position: fixed;
    bottom: calc(56px + env(safe-area-inset-bottom) + 12px);
    left: 16px; right: 16px;
    background: #1e293b;
    color: #fff;
    border-radius: 12px;
    padding: 14px 16px;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 20px rgba(0,0,0,.3);
    animation: snack-in .25s ease;
    z-index: 300;
}
@keyframes snack-in {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
```

## FAB (Floating Action Button)

```css
.fab {
    position: fixed;
    right: 16px;
    bottom: calc(56px + env(safe-area-inset-bottom) + 16px);
    width: 56px; height: 56px;
    border-radius: 16px;
    background: var(--color-brand);
    color: #fff;
    border: none;
    font-size: 24px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(0,0,0,.25);
    cursor: pointer;
    z-index: 50;
    transition: transform .15s;
}
.fab:active { transform: scale(.93); }
```

## Skeleton loading

```css
.skeleton {
    background: linear-gradient(90deg, var(--color-border) 25%, var(--color-bg) 50%, var(--color-border) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 8px;
}
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.skeleton-text  { height: 14px; margin: 6px 0; }
.skeleton-title { height: 20px; width: 60%; margin-bottom: 8px; }
.skeleton-card  { height: 80px; border-radius: 16px; }
```

## Progress / Barra de progreso

```css
.progress-track {
    height: 8px;
    background: var(--color-border);
    border-radius: 4px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    background: var(--color-brand);
    border-radius: 4px;
    transition: width .4s ease;
}
```

## Formularios móvil

```css
/* Input base móvil */
.field { margin-bottom: 18px; }
.field-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}
.field-input {
    width: 100%;
    padding: 14px 16px 14px 44px; /* con ícono */
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px; /* mínimo para evitar zoom iOS */
    font-family: inherit;
    color: #0f172a;
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    -webkit-appearance: none;
}
.field-input:focus {
    border-color: var(--color-brand);
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}
.field-input.has-error {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220,38,38,.10);
}
.field-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 18px;
    pointer-events: none;
}
.field-error {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 6px;
    font-size: 12px;
    color: #dc2626;
    font-weight: 500;
}
```

## Modales y Bottom Sheets pro

```css
/* Overlay */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 200;
    display: flex;
    align-items: flex-end; /* bottom sheet en móvil */
    animation: overlay-in .2s ease;
}
@keyframes overlay-in {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* Modal como bottom sheet en móvil */
.modal {
    background: var(--color-surface);
    border-radius: 20px 20px 0 0;
    width: 100%;
    max-height: 85dvh;
    display: flex;
    flex-direction: column;
    padding-bottom: env(safe-area-inset-bottom);
    animation: sheet-in .22s ease-out;
    overflow: hidden;
}
@keyframes sheet-in {
    from { transform: translateY(40px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

/* Handle de arrastre */
.modal-handle {
    width: 36px; height: 4px;
    background: #cbd5e1;
    border-radius: 2px;
    margin: 12px auto 0;
    flex-shrink: 0;
}

/* Header del modal */
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--color-border);
    flex-shrink: 0;
}
.modal-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
}
.modal-close {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    color: #475569;
    cursor: pointer;
    min-width: 44px; min-height: 44px; /* área táctil */
}

/* Body scrollable */
.modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior: contain;
}

/* Footer con acciones */
.modal-footer {
    padding: 14px 20px;
    border-top: 1px solid var(--color-border);
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    background: #f8fafc;
    flex-shrink: 0;
    padding-bottom: calc(14px + env(safe-area-inset-bottom));
}
```

## Badges de estado — 8 variantes

```css
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
    letter-spacing: .01em;
}
/* Punto de estado (para estados activos/en curso) */
.badge--dot::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

/* ── Modo suave (pastel) ── */
.badge-activo     { background: #dcfce7; color: #15803d; }
.badge-pendiente  { background: #fef9c3; color: #854d0e; }
.badge-en-camino  { background: #dbeafe; color: #1d4ed8; }
.badge-entregado  { background: #d1fae5; color: #047857; }
.badge-cancelado  { background: #fee2e2; color: #b91c1c; }
.badge-rechazado  { background: #fce7f3; color: #9d174d; }
.badge-inactivo   { background: #f1f5f9; color: #475569; }
.badge-revision   { background: #ede9fe; color: #6d28d9; }

/* ── Modo sólido (fondo lleno + texto blanco) ── */
.badge-activo.badge--solid     { background: #16a34a; color: #fff; }
.badge-pendiente.badge--solid  { background: #ca8a04; color: #fff; }
.badge-en-camino.badge--solid  { background: #2563eb; color: #fff; }
.badge-entregado.badge--solid  { background: #059669; color: #fff; }
.badge-cancelado.badge--solid  { background: #dc2626; color: #fff; }
.badge-rechazado.badge--solid  { background: #db2777; color: #fff; }
.badge-inactivo.badge--solid   { background: #64748b; color: #fff; }
.badge-revision.badge--solid   { background: #7c3aed; color: #fff; }
```

**Uso:**
```html
<!-- Modo suave (en tablas, listas) -->
<span class="badge badge-activo badge--dot">Activo</span>
<span class="badge badge-pendiente badge--dot">Pendiente</span>
<span class="badge badge-en-camino badge--dot">En camino</span>

<!-- Modo sólido (estado principal del registro) -->
<span class="badge badge-entregado badge--solid">Entregado</span>
<span class="badge badge-cancelado badge--solid">Cancelado</span>
<span class="badge badge-revision badge--solid">En revisión</span>
```

## Estado vacío

```html
<div class="empty-state">
    <span class="empty-icon">📭</span>
    <h3>Sin movimientos</h3>
    <p>Tus transacciones aparecerán aquí.</p>
    <a class="btn-primary" href="#">Hacer mi primer pedido</a>
</div>
```
```css
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 24px;
    text-align: center;
    gap: 8px;
}
.empty-icon  { font-size: 48px; margin-bottom: 8px; }
.empty-state h3 { font-size: 18px; font-weight: 600; }
.empty-state p  { font-size: 14px; color: var(--color-muted); }
```
