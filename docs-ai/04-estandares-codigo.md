# Estandares de Codigo

## Convenciones Laravel

Usar convenciones Laravel por defecto salvo que exista una razon documentada para desviarse.

## Nombres

- Modelos en singular: `Order`, `Product`, `Customer`.
- Tablas en plural: `orders`, `products`, `customers`.
- Controladores por modulo: `OrderController`, `ProductController`.
- Componentes Livewire con nombre claro: `AdminOrdersTable`.
- Rutas con nombres consistentes: `admin.orders.index`.

## Controladores

Los controladores deben ser delgados:

- Recibir request.
- Validar o delegar validacion.
- Llamar servicios o acciones.
- Retornar vista, redirect o JSON.

## Validaciones

Usar Form Request cuando aplique:

- Formularios grandes.
- Reglas reutilizables.
- Validaciones de modulos criticos.
- API endpoints.

## Codigo limpio

- Simple antes que complejo.
- Legible antes que ingenioso.
- Sin duplicacion innecesaria.
- Comentarios solo cuando aporten claridad.
- Metodos cortos y con responsabilidad clara.

## Base de datos y vistas

- Evitar logica SQL directa en vistas.
- No consultar modelos desde Blade.
- Usar Eloquent, Query Builder, servicios o componentes preparados.
- Las vistas solo deben presentar datos.
