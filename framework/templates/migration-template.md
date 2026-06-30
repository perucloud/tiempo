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

## Alcance por rol

- Campo de propietario si aplica: `affiliate_business_id`, `delivery_driver_id`, `customer_id`.
- Indices para filtros por propietario/estado.
- Reglas para impedir acceso cruzado entre negocios afiliados, repartidores y clientes.

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
- Exposicion de datos globales a roles limitados.
