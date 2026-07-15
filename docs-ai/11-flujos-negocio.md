# Flujos de Negocio

## Flujo del cliente

1. Entra a `/app`.
2. Selecciona negocio afiliado.
3. Agrega productos.
4. Confirma direccion.
5. **Activa geolocalización** (opcional pero recomendado) — el sistema solicita permiso GPS y guarda coordenadas en el pedido.
6. Sube voucher si aplica.
7. Crea pedido.
8. Consulta estado.

## Flujo del operador/dashboard

1. Ingresa a `/admin`.
2. Revisa pedidos nuevos.
3. Verifica pago.
4. Confirma o rechaza pedido.
5. Asigna repartidor.
6. **Hace seguimiento (tracking)** — ve en mapa la posición del repartidor en tiempo real y la ubicación del cliente en el detalle del pedido.
7. Supervisa estados.
8. Atiende incidencias.

## Flujo del repartidor

1. **Activa turno** — el sistema fuerza activación de GPS; sin GPS no puede operar.
2. Queda disponible con tracking activo.
3. Recibe asignación.
4. Recoge pedido.
5. Marca pedido en camino — el operador ve su posición en tiempo real.
6. Entrega pedido.
7. Reporta incidencia si aplica.
8. **Termina turno** — el tracking de GPS se detiene.

El tracking envía posición al sistema cada 10 segundos durante el turno activo.

## Flujo de negocios afiliados

1. Negocio afiliado se registra o es creado por TIEMPO.
2. Se configuran datos, horarios, fotos y promociones.
3. Se agregan categorias y productos propios.
4. Se activa disponibilidad.
5. TIEMPO usa su carta para vender a clientes.

El negocio afiliado no administra pedidos, clientes, pagos ni repartidores.

## Flujo del negocio afiliado

1. Ingresa a su panel limitado.
2. Actualiza perfil e informacion del negocio.
3. Administra carta digital, productos, categorias, fotos, horarios y promociones.
4. TIEMPO opera los pedidos generados por los clientes.

## Flujo de productos

1. Crear categoria.
2. Crear producto.
3. Asociar negocio afiliado.
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

Cliente compra, operador TIEMPO verifica pago, TIEMPO coordina con el negocio afiliado, repartidor entrega y el sistema registra venta, pago, tiempos y estado final.
