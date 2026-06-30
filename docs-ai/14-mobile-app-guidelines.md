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

## Componentes

Usar componentes mobile:

- Cards de negocios afiliados.
- Cards de productos.
- Badges de estado.
- Bottom sheets si aportan claridad.
- Modales simples.
- Empty states claros.
- Loading states livianos.

## Botones

- Grandes y tactiles.
- Texto claro.
- Accion primaria visible.
- Estados disabled y loading.
- Separar acciones destructivas.

## Formularios

- Cortos.
- Campos grandes.
- Teclado adecuado por tipo de dato.
- Errores visibles.
- Autocompletado cuando aplique.
- Evitar formularios largos en una sola pantalla.

## Carrito

- Siempre accesible.
- Mostrar cantidad y total.
- Permitir editar cantidades rapido.
- Confirmar disponibilidad antes de checkout.
- Mostrar subtotal, delivery y total.

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

## Optimizacion para PWA

- Manifest configurado.
- Iconos correctos.
- Color de tema.
- Service worker solo con estrategia clara.
- No cachear datos sensibles sin control.
- Manejar sesion expirada.

## Optimizacion para APK

- `/app` sera la unica seccion empaquetada.
- Landing y Dashboard no se empaquetan.
- Capacitor cargara la app web movil.
- Cambios funcionales deben venir del servidor.
- Recompilar APK solo por permisos, iconos, splash screen, configuracion nativa o plugins.

## Rendimiento

- Carga inicial rapida.
- Imagenes optimizadas.
- Listados paginados o cargados progresivamente.
- Evitar scripts pesados.
- Priorizar respuesta rapida al tocar botones.

## Accesibilidad

- Contraste suficiente.
- Areas tactiles comodas.
- Textos legibles.
- Estados no basados solo en color.
- Labels claros.

## Buenas practicas

- Disenar primero el flujo antes de programar.
- Probar siempre en celular real o viewport movil.
- Evitar componentes de escritorio.
- Mantener interfaz simple.
- Reducir pasos para comprar.
- Validar errores de red, pago y sesion expirada.
