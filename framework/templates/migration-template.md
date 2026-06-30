# Template: Migracion

## Tabla

`nombre_tabla`

## Convenciones

- ID `bigint unsigned`.
- `timestamps`.
- `softDeletes` si aplica.
- Foreign keys claras.
- Indices para busqueda, estados, fechas y relaciones.

## Columnas

| Campo | Tipo | Nullable | Default | Indice | Descripcion |
| --- | --- | --- | --- | --- | --- |
| id | bigint unsigned | no | auto | primary | Identificador |

## Relaciones

- `tabla_id` -> `tabla.id`

## Estados controlados

- `pendiente`
- `activo`
- `inactivo`

## Validaciones relacionadas

- Requeridos.
- Unicos.
- Rangos.
- Formatos.

## Riesgos

- Perdida de historial.
- Borrado en cascada no deseado.
- Falta de indice en filtros frecuentes.
