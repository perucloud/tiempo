# Mobile App Guidelines

Este documento es la guia oficial para desarrollar la App Movil de TIEMPO en `/app`.

## Filosofia Mobile First

`/app` es la interfaz principal para clientes y esta disenada exclusivamente para telefonos moviles.

`/app` es solo para clientes. No debe incluir funciones de SuperAdmin, Admin, Operador, Negocio Afiliado ni Repartidor.

No es una version responsive del dashboard.
No es una copia de la landing.
Debe sentirse como una aplicacion nativa similar a Uber Eats, Rappi o PedidosYa.

## Navegacion

- Usar navegacion inferior para accesos frecuentes.
- Mantener carrito siempre accesible.
- Permitir volver facilmente.
- Evitar menus profundos.
- Priorizar: Inicio, Buscar, Carrito, Pedidos y Perfil.

## Mapa de pantallas

Pantallas base obligatorias para la primera version:

- Inicio: negocios afiliados destacados, categorias, promociones y acceso rapido a busqueda.
- Buscar: busqueda por negocio, producto o categoria.
- Negocio: informacion del negocio afiliado, horarios, categorias y productos disponibles.
- Producto: detalle, fotos, precio, variantes simples y boton agregar.
- Carrito: productos, cantidades, subtotal, delivery y total.
- Checkout: direccion, datos de contacto, metodo de pago y voucher si aplica.
- Pedido creado: resumen, codigo de pedido y estado inicial.
- Seguimiento: estados del pedido, repartidor asignado cuando exista y datos de entrega.
- Historial: pedidos anteriores.
- Perfil: datos del cliente, direcciones y sesion.

Pantallas futuras:

- Cupones o promociones.
- Favoritos.
- Soporte o chat operativo.
- Notificaciones.

## Navegacion inferior

La navegacion inferior debe tener maximo cinco accesos:

- Inicio.
- Buscar.
- Carrito.
- Pedidos.
- Perfil.

Reglas:

- El carrito debe mostrar cantidad de items.
- El tab activo debe ser claro.
- No incluir opciones administrativas.
- No usar sidebar en `/app`.
- Evitar submenus; usar pantallas simples y retorno claro.

## Componentes

Usar componentes mobile:

- Cards de negocios afiliados.
- Cards de productos.
- Badges de estado.
- Bottom sheets si aportan claridad.
- Modales simples.
- Empty states claros.
- Loading states livianos.

## Componentes por pantalla

Inicio:

- Header compacto con ubicacion o zona.
- Buscador visible.
- Chips de categorias.
- Lista de negocios afiliados en cards.
- Banner discreto de promocion si existe.

Negocio:

- Cabecera con foto, nombre, estado abierto/cerrado y tiempo estimado.
- Tabs o chips de categorias del negocio.
- Cards de productos con imagen, nombre, descripcion corta y precio.

Producto:

- Imagen principal.
- Nombre, descripcion y precio.
- Selector de cantidad.
- Observaciones simples.
- Boton principal fijo para agregar.

Carrito:

- Lista editable de productos.
- Botones tactiles para sumar/restar.
- Resumen de costos.
- Boton continuar fijo en la parte inferior.

Seguimiento:

- Timeline de estados.
- Datos del pedido.
- Datos del repartidor solo cuando este asignado.
- Acciones de soporte si aplica.

## Botones

- Grandes y tactiles.
- Texto claro.
- Accion primaria visible.
- Estados disabled y loading.
- Separar acciones destructivas.

Reglas:

- Altura minima recomendada: 44px.
- Acciones principales deben estar cerca del pulgar.
- Evitar mas de una accion primaria por pantalla.
- Usar estados `loading`, `disabled`, `success` y `error`.

## Formularios

- Cortos.
- Campos grandes.
- Teclado adecuado por tipo de dato.
- Errores visibles.
- Autocompletado cuando aplique.
- Evitar formularios largos en una sola pantalla.

Formularios iniciales:

- Registro/login de cliente.
- Direccion de entrega.
- Datos de contacto.
- Subida de voucher.
- Perfil del cliente.

Reglas:

- Validar antes de enviar.
- Mostrar errores debajo del campo.
- Dividir checkout en pasos cortos si crece.
- No pedir datos que no sean necesarios para comprar.

## Carrito

- Siempre accesible.
- Mostrar cantidad y total.
- Permitir editar cantidades rapido.
- Confirmar disponibilidad antes de checkout.
- Mostrar subtotal, delivery y total.

Reglas:

- El carrito debe persistir durante la sesion.
- Si el negocio cierra o un producto no esta disponible, informar antes de crear el pedido.
- No mezclar productos de varios negocios en la primera version salvo decision explicita posterior.
- Permitir vaciar carrito con confirmacion.

## Flujo de compra

Flujo ideal:

1. Ver negocios afiliados.
2. Elegir negocio afiliado.
3. Ver productos.
4. Agregar al carrito.
5. Confirmar direccion.
6. Subir voucher si aplica.
7. Crear pedido.
8. Seguir estado.

El flujo debe ser rapido, claro y usable con una sola mano.

## Estados del pedido para cliente

El cliente debe ver estados simples y comprensibles:

- Pendiente de pago.
- Pago en revision.
- Pedido aprobado.
- En preparacion.
- Repartidor asignado.
- En camino.
- Entregado.
- Rechazado o cancelado.

Reglas:

- No mostrar estados internos complejos.
- Cada estado debe tener texto corto.
- Si hay rechazo, explicar motivo de forma clara.
- Si hay demora, mostrar mensaje operativo simple.

## Optimizacion para PWA

- Manifest configurado.
- Iconos correctos.
- Color de tema.
- Service worker solo con estrategia clara.
- No cachear datos sensibles sin control.
- Manejar sesion expirada.

Reglas PWA:

- La app debe cargar bien en Chrome Android.
- Debe respetar areas seguras y pantalla completa cuando aplique.
- Debe funcionar en red lenta con mensajes claros.
- Los datos privados deben venir del servidor y no quedar cacheados sin estrategia.

## Optimizacion para APK

- `/app` sera la unica seccion empaquetada.
- Landing y Dashboard no se empaquetan.
- Capacitor cargara la app web movil.
- Cambios funcionales deben venir del servidor.
- Recompilar APK solo por permisos, iconos, splash screen, configuracion nativa o plugins.

Reglas APK:

- No depender de rutas de desktop.
- Evitar popups incompatibles con WebView.
- Probar navegacion atras de Android.
- Mantener assets ligeros para mejorar carga inicial.

## Rendimiento

- Carga inicial rapida.
- Imagenes optimizadas.
- Listados paginados o cargados progresivamente.
- Evitar scripts pesados.
- Priorizar respuesta rapida al tocar botones.

Metas iniciales:

- Pantallas principales deben sentirse instantaneas al navegar.
- Listados largos deben paginarse o cargar progresivamente.
- Imagenes de productos y negocios deben tener tamanos controlados.
- No bloquear la UI durante checkout, subida de voucher o confirmacion.

## Accesibilidad

- Contraste suficiente.
- Areas tactiles comodas.
- Textos legibles.
- Estados no basados solo en color.
- Labels claros.

Reglas de accesibilidad:

- Texto minimo legible en celular.
- Botones y campos con foco visible.
- Mensajes de error con texto, no solo color.
- Imagenes importantes con descripcion cuando aplique.
- Flujos clave usables con lector de pantalla en lo esencial.

## Criterios para iniciar desarrollo de `/app`

Antes de programar FASE 08, validar:

- Pantallas base definidas.
- Navegacion inferior definida.
- Componentes principales definidos.
- Flujo de compra completo definido.
- Estados del pedido definidos para cliente.
- Criterios PWA/APK entendidos.
- Separacion estricta entre `/app`, `/admin` y `/`.

## Buenas practicas

- Disenar primero el flujo antes de programar.
- Probar siempre en celular real o viewport movil.
- Evitar componentes de escritorio.
- Mantener interfaz simple.
- Reducir pasos para comprar.
- Validar errores de red, pago y sesion expirada.
