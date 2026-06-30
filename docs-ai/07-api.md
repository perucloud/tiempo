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

- Cliente: negocios disponibles, productos, carrito, pedidos propios, pagos propios, perfil y direcciones.
- Repartidor: pedidos asignados, ruta, cliente necesario para entrega y estados.
- Negocio Afiliado: perfil, carta, productos, categorias, fotos, horarios y promociones propias.
- Admin/Operador: endpoints internos autorizados para operacion de TIEMPO.

## Laravel

Usar:

- API Resources.
- Form Requests.
- Middleware.
- Policies si aplica.
- Paginacion nativa.
