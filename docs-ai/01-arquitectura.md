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

## Estructura de carpetas recomendada

- `routes/web.php`: landing y rutas web generales.
- `routes/admin.php`: dashboard bajo `/admin`.
- `routes/app.php`: PWA bajo `/app`.
- `routes/api.php`: API bajo `/api`.
- `app/Http/Controllers/Admin`: controladores admin.
- `app/Http/Controllers/App`: controladores PWA.
- `app/Http/Controllers/Api`: controladores API.
- `app/Livewire/Admin`: componentes admin.
- `app/Livewire/App`: componentes PWA.
- `app/Services` o `app/Actions`: logica de negocio.
- `resources/views/admin`: vistas admin.
- `resources/views/app`: vistas PWA.
- `resources/views/web`: landing.

## Responsabilidades

- Landing `/`: presentar TIEMPO y captar usuarios.
- Dashboard `/admin`: operar pedidos, pagos, restaurantes, productos, repartidores y reportes.
- App/PWA `/app`: experiencia mobile para clientes.
- API `/api`: entregar datos JSON a la app movil e integraciones internas.

## Regla clave

No mezclar logica de negocio en vistas Blade ni componentes visuales. Las vistas muestran datos; los servicios, acciones y modelos resuelven reglas.
