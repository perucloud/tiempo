# Snippet: PWA y Capacitor

PWA base:

- Manifest.
- Iconos.
- Color de tema.
- Layout mobile-first.
- Service worker solo con estrategia clara.
- Exclusiva para clientes en `/app`.

Capacitor:

- Agregar cuando `/app` este estable.
- La APK carga la app web movil.
- La logica de negocio queda en servidor/API.
- No empaquetar Landing ni Dashboard.

Solo recompilar APK si cambian:

- Permisos Android.
- Iconos.
- Splash screen.
- Configuracion nativa.
- Plugins Capacitor.

Probar:

- Android Chrome.
- Instalacion PWA.
- Red lenta.
- Sesion expirada.
