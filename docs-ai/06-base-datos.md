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
- `categorias`
- `restaurantes`
- `productos`
- `clientes`
- `repartidores`
- `pedidos`
- `pedido_detalles`
- `pagos`
- `pedido_estados`
