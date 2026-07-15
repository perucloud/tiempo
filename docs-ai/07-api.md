# API

## Ubicacion

La API interna debe vivir bajo `/api`.

Separar:

- API para app movil.
- API administrativa si en el futuro se necesita.
- API de negocio afiliado si se expone gestion propia.
- API de repartidor si se expone operacion movil.
- Endpoints publicos si existen.

## Respuestas JSON

Usar respuestas JSON uniformes:

- `data`: informacion principal.
- `message`: mensaje opcional.
- `errors`: errores de validacion.
- `meta`: paginacion o informacion adicional.

## Codigos HTTP

Usar codigos correctos:

- `200`: correcto.
- `201`: creado.
- `204`: sin contenido.
- `400`: solicitud invalida.
- `401`: no autenticado.
- `403`: no autorizado.
- `404`: no encontrado.
- `422`: validacion fallida.
- `500`: error interno.

## Endpoints para app movil

Endpoints previstos:

- Negocios afiliados.
- Categorias.
- Productos.
- Carrito o checkout.
- Pedidos.
- Pagos.
- Perfil del cliente.
- Direcciones.

## Seguridad

- Autenticacion cuando aplique.
- No exponer datos sensibles.
- Validar todos los inputs.
- Aplicar rate limit en endpoints sensibles.
- Separar API publica de API administrativa.
- Aislar datos por rol: cliente, repartidor, negocio afiliado, operador/admin.
- No exponer pedidos, clientes, pagos o repartidores globales a negocios afiliados.

## Alcance por consumidor

- Cliente: negocios disponibles, productos, carrito, pedidos propios, pagos propios, perfil, direcciones y envío de ubicación propia al confirmar pedido.
- Repartidor: pedidos asignados, ruta, cliente necesario para entrega, estados y envío de posición GPS durante el turno.
- Negocio Afiliado: perfil, carta, productos, categorias, fotos, horarios y promociones propias.
- Admin/Operador: endpoints internos autorizados, tracking de repartidores activos y ubicación de clientes en pedidos.

## Endpoints de geolocalización

```
POST /api/v1/pedidos/{codigo}/ubicacion        — cliente guarda coordenadas al confirmar pedido
POST /api/v1/repartidores/ubicacion            — repartidor actualiza posición GPS cada 10s
GET  /api/v1/repartidores/{id}/ubicacion       — operador consulta posición de un repartidor
GET  /api/v1/repartidores/ubicaciones-activas  — operador obtiene mapa general de repartidores activos
```

Ver especificación completa en `docs-ai/17-geolocalizacion.md`.

## Laravel

Usar:

- API Resources.
- Form Requests.
- Middleware.
- Policies si aplica.
- Paginacion nativa.
