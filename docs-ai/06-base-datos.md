# Base de Datos

## Convenciones de tablas

- Tablas en plural.
- Nombres claros y consistentes.
- Preferir ingles para nombres tecnicos.
- Usar migraciones Laravel.
- No usar dumps SQL como fuente principal.

## IDs y timestamps

- IDs `bigint unsigned`.
- Usar `timestamps`.
- Usar `softDeletes` cuando aplique.
- No borrar informacion critica si debe mantenerse historial.

## Relaciones

- Foreign keys claras.
- Nombres de columnas consistentes: `user_id`, `pedido_id`, `restaurant_id`.
- Definir relaciones en modelos Eloquent.
- Cuidar integridad de pedidos, pagos y usuarios.

## Indices

Agregar indices en campos usados para:

- Busqueda.
- Filtros.
- Estados.
- Fechas.
- Relaciones.
- Email o telefono cuando aplique.

## Estados

Usar enums o strings controlados.

Ejemplos:

- Pedido: `pendiente`, `confirmado`, `preparando`, `en_camino`, `entregado`, `cancelado`.
- Pago: `pendiente`, `aprobado`, `rechazado`.
- Repartidor: `disponible`, `ocupado`, `inactivo`.

## Tablas iniciales

- `users`
- `roles`
- `permissions`
- `role_permissions`
- `categorias`
- `negocios_afiliados`
- `productos`
- `clientes`
- `repartidores`
- `pedidos`
- `pedido_detalles`
- `pagos`
- `pedido_estados`

## Tablas de geolocalización

- `repartidor_ubicaciones` — log de posiciones GPS del repartidor durante el turno.

Campos de geolocalización en tablas existentes:

- `pedidos`: `latitud_cliente`, `longitud_cliente`, `geolocalizacion_at` (nullable).
- `repartidores`: `latitud_actual`, `longitud_actual`, `ubicacion_actualizada_at` (nullable).

Coordenadas: `decimal(10,7)` — 7 decimales dan precisión de ~1cm, suficiente para delivery urbano.

Ver especificación completa en `docs-ai/17-geolocalizacion.md`.

## Modelo multi-rol

Reglas:

- Todo usuario debe tener rol claro.
- Los usuarios de negocio afiliado deben relacionarse con un unico negocio o con los negocios autorizados.
- Los repartidores solo deben acceder a pedidos asignados.
- Los clientes deben separarse logicamente del acceso administrativo.
- Los permisos deben controlar modulos y acciones, no solo menus.

## Negocios afiliados

`negocios_afiliados` representa restaurantes, cafeterias, pollerias, pizzerias, licorerias, bodegas, farmacias u otros comercios.

Debe permitir informacion comercial, horarios, fotos, categorias, productos y promociones propias.
