# Flujos de Negocio

## Flujo del cliente

1. Entra a `/app`.
2. Selecciona restaurante.
3. Agrega productos.
4. Confirma direccion.
5. Sube voucher si aplica.
6. Crea pedido.
7. Consulta estado.

## Flujo del operador/dashboard

1. Ingresa a `/admin`.
2. Revisa pedidos nuevos.
3. Verifica pago.
4. Confirma o rechaza pedido.
5. Asigna repartidor.
6. Supervisa estados.
7. Atiende incidencias.

## Flujo del repartidor

1. Queda disponible.
2. Recibe asignacion.
3. Recoge pedido.
4. Marca pedido en camino.
5. Entrega pedido.
6. Reporta incidencia si aplica.

## Flujo de restaurantes

1. Restaurante se registra desde admin.
2. Se configuran datos y horarios.
3. Se agregan productos.
4. Se activa disponibilidad.
5. Recibe pedidos segun operacion definida.

## Flujo de productos

1. Crear categoria.
2. Crear producto.
3. Asociar restaurante.
4. Definir precio.
5. Activar disponibilidad.

## Flujo de verificacion de pagos

1. Cliente sube voucher Yape/Plin.
2. Operador revisa comprobante.
3. Aprueba o rechaza.
4. El pedido avanza solo si corresponde.

## Flujo de estados del pedido

Estados sugeridos:

- `pendiente`
- `pago_en_revision`
- `confirmado`
- `preparando`
- `listo`
- `asignado`
- `en_camino`
- `entregado`
- `cancelado`

## Flujo de venta completa

Cliente compra, operador verifica pago, restaurante prepara, repartidor entrega y el sistema registra venta, pago, tiempos y estado final.
