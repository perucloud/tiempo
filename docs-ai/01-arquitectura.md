# Arquitectura

## Arquitectura Laravel

TIEMPO debe seguir una arquitectura Laravel limpia, modular y mantenible.

Usar convenciones nativas de Laravel siempre que sea posible: rutas, controladores, modelos, migraciones, seeders, middleware, Form Requests, Policies, Blade y Livewire.

## Separacion por capas

- Rutas: definen entrada HTTP y middleware.
- Controladores: reciben requests y coordinan respuestas.
- Modelos: representan entidades y relaciones.
- Servicios/Actions: contienen reglas de negocio.
- Vistas Blade: renderizan HTML.
- Componentes Livewire: manejan interaccion dinamica.
- API Resources: transforman respuestas JSON.

## Separacion arquitectonica por interfaz

TIEMPO se divide en cuatro superficies independientes:

- Landing `/`: marketing, SEO, informacion publica y captacion.
- Dashboard `/admin`: operacion interna de administradores, operadores y duenos. Desktop-first con experiencia completa y version movil simplificada para gestion rapida.
- App Movil `/app`: experiencia principal del cliente, exclusiva para telefonos moviles.
- API `/api`: capa JSON para la app movil e integraciones internas.

Cada superficie debe tener layouts, componentes, navegacion y experiencia de usuario independientes.

## Arquitectura de negocio

TIEMPO centraliza la operacion del delivery. Los negocios afiliados son proveedores de carta/productos, no operadores del sistema.

Reglas de dominio:

- TIEMPO administra pedidos, clientes, pagos, repartidores, estados y reportes globales.
- El negocio afiliado solo administra su propia informacion comercial y carta digital.
- El operador de TIEMPO confirma/rechaza pedidos, verifica pagos y asigna repartidores.
- El repartidor solo opera pedidos asignados.
- El cliente solo interactua desde `/app`.

Los permisos deben reflejar esta separacion y nunca depender solo de ocultar botones en la UI.

Reglas:

- La App Movil no debe reutilizar layout del Dashboard.
- El Dashboard movil no debe reutilizar las tablas grandes del Dashboard desktop.
- El Dashboard no debe comportarse como landing.
- La Landing no debe contener logica operativa.
- La API no debe retornar HTML.
- La logica de negocio debe vivir en servicios, acciones o modelos, no en vistas.

## Estructura de carpetas recomendada

- `routes/web.php`: landing y rutas web generales.
- `routes/admin.php`: dashboard bajo `/admin`.
- `routes/app.php`: PWA bajo `/app`.
- `routes/api.php`: API bajo `/api`.
- `app/Http/Controllers/Admin`: controladores admin.
- `app/Http/Controllers/App`: controladores PWA.
- `app/Http/Controllers/Api`: controladores API.
- `app/Http/Controllers/Affiliate`: controladores para negocio afiliado si se separan del admin.
- `app/Livewire/Admin`: componentes admin.
- `app/Livewire/App`: componentes PWA.
- `app/Livewire/Affiliate`: componentes para perfil/carta del negocio afiliado si aplica.
- `app/Services` o `app/Actions`: logica de negocio.
- `resources/views/admin`: vistas admin.
- `resources/views/app`: vistas PWA.
- `resources/views/web`: landing.

Layouts recomendados:

- `resources/views/layouts/web`: layout de landing.
- `resources/views/layouts/admin`: layout del dashboard.
- `resources/views/layouts/admin-mobile`: patrones responsive simplificados para operacion movil dentro de `/admin`.
- `resources/views/layouts/app-mobile`: layout exclusivo para app movil.

## Responsabilidades

- Landing `/`: presentar TIEMPO y captar usuarios.
- Dashboard `/admin`: operar pedidos, pagos, negocios afiliados, productos, repartidores y reportes. En desktop debe ofrecer la experiencia completa; en movil debe priorizar gestion rapida de pedidos, pagos, estados, repartidores y ventas.
- App/PWA `/app`: experiencia mobile principal y exclusiva para clientes, optimizada para uso tactil y futura APK.
- API `/api`: entregar datos JSON a la app movil e integraciones internas.

## Roles y permisos

- SuperAdmin: acceso total y gestion de permisos.
- Admin: acceso a modulos autorizados.
- Operador: operacion diaria de pedidos, pagos, estados y repartidores.
- Negocio Afiliado: acceso limitado a su propio negocio, carta, productos, categorias, fotos, horarios y promociones.
- Repartidor: pedidos asignados, ruta, cliente y estados.
- Cliente: compra, comprobantes, historial, seguimiento y perfil.

## Geolocalización y tracking

El sistema incorpora dos capas de geolocalización:

- **Cliente**: captura puntual de coordenadas al confirmar pedido desde `/app`. Browser Geolocation API. Coordenadas guardadas en `pedidos`.
- **Repartidor**: tracking GPS continuo durante el turno. `watchPosition()` + POST a API cada 10s. Posición guardada en `repartidores` + log en `repartidor_ubicaciones`.

Mapas: Leaflet.js + OpenStreetMap (CDN, sin API key). Solo se cargan en las vistas que renderizan mapas.

El tracking del repartidor es obligatorio — el sistema impide operar sin GPS activo.

El operador hace seguimiento desde `/admin` con mapa en tiempo real.

Ver especificación completa en `docs-ai/17-geolocalizacion.md`.

## Regla clave

No mezclar logica de negocio en vistas Blade ni componentes visuales. Las vistas muestran datos; los servicios, acciones y modelos resuelven reglas.
