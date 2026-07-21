# PLAN — Autenticación y Perfil Completo del Cliente (PWA)

## Objetivo
Implementar registro/login propio para clientes de la PWA `/app`,
sistema de direcciones múltiples, perfil editable y campos automáticos del sistema.

---

## FASE A — Base de datos + Guard + Modelo ✅
- [x] Migración: nuevas columnas en `clientes` (password, codigo_cliente, etc.)
- [x] Migración: tabla `cliente_direcciones`
- [x] `Cliente` model → extends `Authenticatable`
- [x] `config/auth.php` → guard `cliente`
- [x] Middleware `EnsureClienteIsAuthenticated`
- [x] `routes/app.php` → grupos public / guest / auth
- [x] commit + push

## FASE B — Controladores y Vistas de Auth ✅
- [x] `App\Http\Controllers\App\Auth\LoginController`
- [x] `App\Http\Controllers\App\Auth\RegisterController`
- [x] `App\Http\Controllers\App\Auth\ForgotPasswordController`
- [x] Vista `app/auth/bienvenida.blade.php` (landing para invitados)
- [x] Vista `app/auth/login.blade.php`
- [x] Vista `app/auth/registro.blade.php`
- [x] Vista `app/auth/recuperar.blade.php` (OTP 4-pasos)
- [x] Layout `layouts/app-auth.blade.php`
- [x] CSS auth en `app-mobile.css`
- [x] commit + push

## FASE C — Flujo de Pedido Actualizado ✅
- [x] `HomeController` → redirige a login si no autenticado (via cliente.auth)
- [x] `OrderController` → usa cliente autenticado
- [x] `OrderCreator` → acepta cliente pre-autenticado (backward-compatible)
- [x] `home.blade.php` → checkout pre-relleno con datos del cliente
- [x] `app-mobile.blade.php` → bottom nav solo para @auth('cliente')
- [x] commit + push

## FASE D — Direcciones + Info de Entrega ✅
- [x] Modelo `ClienteDireccion`
- [x] `DireccionController` (CRUD: index, store, update, destroy, setPredeterminada)
- [x] Rutas de direcciones (bajo cliente.auth)
- [x] Perfil tab "Direcciones" con Leaflet map
- [x] commit + push

## FASE E — Perfil Completo Editable ✅
- [x] `PerfilController` (show, update, updatePassword, updateFoto, pedidos)
- [x] Vista `app/perfil.blade.php` con 4 tabs:
  - [x] Mis datos (personal + contacto + cambiar contraseña + logout + stats)
  - [x] Direcciones (lista + form + mapa)
  - [x] Pedidos (historial con link a tracking)
  - [x] Preferencias (notificaciones toggles + método pago)
- [x] commit + push

## FASE F — Campos Automáticos del Sistema ✅
- [x] Observer `ClienteObserver` → genera `codigo_cliente` (CLI-000001)
- [x] Observer `PedidoObserver` → actualiza `total_pedidos`, `total_gastado`, `ultimo_pedido_at`
- [x] Login actualiza `ultimo_acceso`, `ip_ultimo_acceso`
- [x] `PushSubscriptionController` → usa guard `cliente`
- [x] 219 tests pasando — suites actualizadas para nuevo flujo auth
- [x] commit + push

---

## Pendiente (fuera del plan base — requiere autorización/credenciales)

- [ ] **RENIEC API** — validación de DNI en perfil (datos personales)
- [ ] **Google OAuth** — botón presente en vistas, desactivado hasta tener credenciales
- [ ] **Facebook OAuth** — ídem
- [ ] **VAPID keys** — configurar en `.env` para activar push notifications en producción
  ```env
  WEBPUSH_PUBLIC_KEY=...
  WEBPUSH_PRIVATE_KEY=...
  WEBPUSH_SUBJECT=mailto:...
  ```
