# PWA y APK

## App movil

`/app` sera la app movil/PWA de TIEMPO.

Debe ser mobile-first y enfocada en compra rapida.

`/app` sera la unica seccion que se convertira en APK. La landing `/` y el dashboard `/admin` nunca deben empaquetarse como APK.

## PWA

Cuando se implemente, debe incluir:

- Manifest configurado.
- Iconos.
- Nombre de aplicacion.
- Color de tema.
- Service worker cuando aplique.
- Estrategia clara de cache.

No cachear datos sensibles sin control.

## Experiencia `/app`

Debe permitir:

- Ver negocios afiliados disponibles.
- Ver productos.
- Agregar al carrito.
- Confirmar pedido.
- Subir voucher si aplica.
- Consultar estado del pedido.
- Ver historial.

`/app` es solo para clientes. No debe incluir pantallas de operador, negocio afiliado, repartidor ni administracion.

## Capacitor

Preparar compatibilidad con Capacitor despues de tener la PWA estable.

La APK cargara la app web movil.

La APK utilizara Capacitor como contenedor nativo para la experiencia mobile de `/app`.

## Actualizaciones

Los cambios del sistema se actualizan desde el servidor.

Las actualizaciones funcionales de la app deben venir del servidor siempre que no dependan de cambios nativos.

Solo recompilar APK si cambian:

- Permisos Android.
- Iconos.
- Splash screen.
- Configuracion nativa.
- Plugins Capacitor.

## Pruebas

Probar `/app` en:

- Chrome Android.
- Pantallas pequenas.
- Red lenta.
- Modo instalable.
- Sesion expirada.
