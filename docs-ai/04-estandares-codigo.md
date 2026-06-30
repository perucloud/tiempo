# Estandares de Codigo

## Convenciones Laravel

Usar convenciones Laravel por defecto salvo que exista una razon documentada para desviarse.

## Nombres

- Modelos en singular: `Order`, `Product`, `Customer`.
- Tablas en plural: `orders`, `products`, `customers`.
- Controladores por modulo: `OrderController`, `ProductController`.
- Componentes Livewire con nombre claro: `AdminOrdersTable`.
- Rutas con nombres consistentes: `admin.orders.index`.

Nombres de dominio recomendados:

- `AffiliateBusiness` para negocio afiliado.
- `DeliveryDriver` para repartidor.
- `Customer` para cliente.
- `Order` para pedido.
- `Payment` para pago.

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

## Reglas de dominio en codigo

- No llamar restaurante a todo negocio afiliado si el modulo admite bodegas, farmacias u otros comercios.
- No mezclar permisos de Cliente, Repartidor, Negocio Afiliado y Operador.
- No permitir que un Negocio Afiliado consulte pedidos, clientes, pagos o repartidores globales.
- No permitir que un Repartidor consulte pedidos no asignados.

## Base de datos y vistas

- Evitar logica SQL directa en vistas.
- No consultar modelos desde Blade.
- Usar Eloquent, Query Builder, servicios o componentes preparados.
- Las vistas solo deben presentar datos.
