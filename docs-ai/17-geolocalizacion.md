# Geolocalización y Tracking

## Objetivo

Incorporar geolocalización del cliente al confirmar pedido y tracking en tiempo real del repartidor durante el delivery, visible para operadores desde `/admin`.

## Dos componentes independientes

### 1. Geolocalización del cliente (puntual)

- **Cuándo**: al confirmar el pedido en `/app`.
- **Cómo**: Browser Geolocation API (`navigator.geolocation.getCurrentPosition`).
- **Qué se guarda**: `latitud_cliente` y `longitud_cliente` en la tabla `pedidos`.
- **Para qué**: el operador y el repartidor ven la ubicación exacta en el detalle del pedido.
- **Si rechaza**: el flujo continúa con solo la dirección de texto — la geolocalización es complementaria, no bloqueante.
- **Privacidad**: no se guarda historial de ubicaciones del cliente; solo la del momento del pedido.

### 2. GPS del repartidor (tracking en tiempo real)

- **Cuándo**: al activar turno / al recibir un pedido asignado.
- **Cómo**: `navigator.geolocation.watchPosition()` + POST a la API cada 10 segundos.
- **Qué se guarda**: `latitud_actual` y `longitud_actual` en `repartidores` + historial en `repartidor_ubicaciones`.
- **Para qué**: el operador sigue en mapa la posición de cada repartidor activo.
- **Obligatorio**: el sistema bloquea la operación si el repartidor no activa el GPS.
- **Se detiene**: al terminar turno, cerrar sesión o no tener pedidos activos.

## Decisiones técnicas

| Componente | Decisión | Razón |
|---|---|---|
| Mapa | Leaflet.js + OpenStreetMap | Gratuito, sin API key, open source |
| Tracking | Polling cada 10s via API | Simple, sin WebSockets, suficiente para delivery local |
| Actualización admin | setInterval cada 10-15s via JS | El operador ve posición casi en tiempo real |
| Cliente | Una sola captura al confirmar | No necesita tiempo real |
| Historial repartidor | Tabla `repartidor_ubicaciones` | Permite ver ruta del día si se necesita |

## Modelo de datos

### Cambios en tabla `pedidos`

```
latitud_cliente         decimal(10,7)   nullable
longitud_cliente        decimal(10,7)   nullable
geolocalizacion_at      timestamp       nullable
```

### Cambios en tabla `repartidores`

```
latitud_actual          decimal(10,7)   nullable
longitud_actual         decimal(10,7)   nullable
ubicacion_actualizada_at timestamp      nullable
```

### Nueva tabla `repartidor_ubicaciones`

```
id                  bigint unsigned
repartidor_id       FK repartidores
pedido_id           FK pedidos nullable
latitud             decimal(10,7)
longitud            decimal(10,7)
created_at          timestamp
```

Índices: `repartidor_id`, `pedido_id`, `created_at`.

No usa `updated_at` ni `softDeletes` — es un log de posición, no un registro editable.

## Endpoints API

### Cliente — guardar ubicación del pedido

```
POST /api/v1/pedidos/{codigo}/ubicacion
Body: { latitud, longitud }
Auth: ninguna (código del pedido actúa como referencia)
Respuesta: 200 con mensaje de confirmación
```

### Repartidor — actualizar posición (llamado cada 10s)

```
POST /api/v1/repartidores/ubicacion
Body: { repartidor_id, latitud, longitud }
Auth: token de repartidor (a implementar en fase de auth repartidor)
Actualiza: latitud_actual, longitud_actual, ubicacion_actualizada_at en repartidores
Registra: nueva fila en repartidor_ubicaciones
```

### Operador — posición actual de un repartidor

```
GET /api/v1/repartidores/{id}/ubicacion
Auth: solo admin/operador
Retorna: latitud, longitud, ultima actualización
```

### Operador — todos los repartidores activos con posición

```
GET /api/v1/repartidores/ubicaciones-activas
Auth: solo admin/operador
Retorna: array de repartidores disponibles/ocupados con latitud y longitud actuales
```

## Archivos JS a crear

### `public/js/geolocalizacion-cliente.js`

- Solicita permiso de geolocalización al confirmar pedido.
- Si acepta: envía coordenadas al endpoint del pedido.
- Si rechaza: muestra mensaje informativo y continúa el flujo normal.
- Se carga solo en la vista de confirmación de pedido de `/app`.

### `public/js/tracking-repartidor.js`

- Llama a `watchPosition()` al iniciar turno.
- Cada 10s envía posición al endpoint de repartidor.
- Muestra estado: GPS activo / sin señal / detenido.
- Bloquea inicio de turno si el GPS no está disponible.

### `public/js/mapa-admin.js`

- Inicializa mapa Leaflet.
- Coloca marcador del cliente en detalle de pedido.
- Coloca marcadores de repartidores activos en dashboard.
- Refresca posición de repartidores cada 15s via GET a la API.
- Leaflet.js se carga desde CDN solo en las vistas que lo necesitan.

## Vistas afectadas

### `/app` — confirmación de pedido

- Botón "Activar mi ubicación" antes de confirmar.
- Estado visual: ubicación capturada / sin ubicación.
- Si rechaza: solo dirección de texto, flujo continúa.

### `/repartidor/{id}/turno` — panel del repartidor (IMPLEMENTADO en FASE 29)

- Vista dedicada mobile-first para el celular del repartidor.
- Layout `layouts/courier.blade.php` con CSS propio `public/css/courier.css`.
- Card de identidad: nombre, teléfono, estado actual del repartidor.
- Indicador circular animado de GPS (idle / loading / active / error).
- Badge de estado GPS con textos específicos por error (permiso, señal, timeout).
- Coordenadas actuales en tiempo real mientras el turno está activo.
- Botón "Iniciar turno" → activa `watchPosition()` + POST cada 10s.
- Botón "Terminar turno" → limpia watch e interval.
- Instrucciones de uso en pantalla para el repartidor.
- Ruta: `GET /repartidor/{repartidor}/turno` → `Courier\ShiftController@show`.
- El operador accede a la URL desde la lista de repartidores (botón "GPS") y la comparte al repartidor.

### `/admin/orders/{id}` — detalle de pedido

- Mapa pequeño con pin de la ubicación del cliente (si fue capturada).
- Coordenadas visibles como texto de respaldo.

### `/admin` — dashboard

- Panel "Repartidores en ruta" con mapa de posiciones activas.

### `/admin/couriers/tracking` — tracking operativo

- Vista dedicada con mapa general de todos los repartidores activos.
- Lista lateral con nombre, estado y última actualización.

## Seguridad

- Coordenadas del cliente son datos personales — no exponer en endpoints públicos.
- Endpoint de repartidor requiere autenticación.
- Ubicaciones de repartidores solo visibles para admin/operador.
- Coordenadas del cliente solo visibles para admin, operador y repartidor asignado al pedido.
- No retornar ubicaciones de repartidores a otros repartidores ni clientes.
- Rate limit en endpoint de actualización de posición (máximo 1 request cada 8s por repartidor).

## Archivos a crear en implementación

```
database/migrations/2026_07_01_000001_add_geolocation_to_pedidos_table.php     ✅
database/migrations/2026_07_01_000002_add_ubicacion_to_repartidores_table.php  ✅
database/migrations/2026_07_01_000003_create_repartidor_ubicaciones_table.php  ✅
app/Http/Controllers/Api/GeolocationController.php                             ✅
app/Http/Controllers/Courier/ShiftController.php                               ✅ (FASE 29)
app/Services/GeolocationService.php                                            ✅
resources/views/admin/orders/edit.blade.php     (sección mapa cliente)         ✅
resources/views/admin/couriers/tracking.blade.php                              ✅
resources/views/courier/turno.blade.php                                        ✅ (FASE 29)
resources/views/layouts/courier.blade.php                                      ✅ (FASE 29)
public/css/courier.css                                                         ✅ (FASE 29)
public/js/geolocalizacion-cliente.js                                           ✅
public/js/tracking-repartidor.js                                               ✅
public/js/mapa-admin.js                                                        ✅
routes/courier.php                                                             ✅ (FASE 29)
```

Leaflet.js se carga via CDN desde el layout o vista — no instalar como dependencia npm.

## CDN de Leaflet

```html
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

Solo incluir en vistas que renderizan mapas.
