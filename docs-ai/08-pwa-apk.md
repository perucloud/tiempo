# PWA y APK

## App movil

`/app` sera la app movil/PWA de TIEMPO.

Debe ser mobile-first y enfocada en compra rapida.

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

- Ver restaurantes.
- Ver productos.
- Agregar al carrito.
- Confirmar pedido.
- Subir voucher si aplica.
- Consultar estado del pedido.
- Ver historial.

## Capacitor

Preparar compatibilidad con Capacitor despues de tener la PWA estable.

La APK cargara la app web movil.

## Actualizaciones

Los cambios del sistema se actualizan desde el servidor.

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
