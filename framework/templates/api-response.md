# Template: Respuesta API

## Formato exitoso

```json
{
  "data": {},
  "message": "Operacion realizada correctamente",
  "meta": {}
}
```

## Formato de error

```json
{
  "message": "La solicitud no pudo procesarse",
  "errors": {}
}
```

## Codigos HTTP

- `200`: consulta correcta.
- `201`: recurso creado.
- `204`: sin contenido.
- `401`: no autenticado.
- `403`: sin permiso.
- `404`: no encontrado.
- `422`: validacion fallida.
- `500`: error interno.

## Reglas

- No exponer datos sensibles.
- Usar API Resources.
- Paginar colecciones grandes.
- Validar filtros y ordenamientos.
- Filtrar respuestas por rol y propietario.
- Negocio Afiliado no recibe pedidos/clientes/pagos/repartidores globales.
- Repartidor no recibe pedidos no asignados.
- Cliente no recibe datos de otros clientes.
