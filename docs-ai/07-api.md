# API

## Ubicacion

La API interna debe vivir bajo `/api`.

Separar:

- API para app movil.
- API administrativa si en el futuro se necesita.
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

- Restaurantes.
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

## Laravel

Usar:

- API Resources.
- Form Requests.
- Middleware.
- Policies si aplica.
- Paginacion nativa.
