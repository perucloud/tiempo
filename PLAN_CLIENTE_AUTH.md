# PLAN — Autenticación y Perfil Completo del Cliente (PWA)

## Objetivo
Implementar registro/login propio para clientes de la PWA `/app`,
sistema de direcciones múltiples, perfil editable y campos automáticos del sistema.

---

## FASE A — Base de datos + Guard + Modelo
- [ ] Migración: nuevas columnas en `clientes` (password, codigo_cliente, etc.)
- [ ] Migración: tabla `cliente_direcciones`
- [ ] `Cliente` model → extends `Authenticatable`
- [ ] `config/auth.php` → guard `cliente`
- [ ] Middleware `EnsureClienteIsAuthenticated`
- [ ] `routes/app.php` → grupos public / guest / auth
- [ ] commit + push

## FASE B — Controladores y Vistas de Auth
- [ ] `App\Http\Controllers\App\Auth\LoginController`
- [ ] `App\Http\Controllers\App\Auth\RegisterController`
- [ ] `App\Http\Controllers\App\Auth\ForgotPasswordController`
- [ ] Vista `app/auth/login.blade.php`
- [ ] Vista `app/auth/registro.blade.php`
- [ ] Vista `app/auth/recuperar.blade.php`
- [ ] CSS auth en `app-mobile.css`
- [ ] commit + push

## FASE C — Flujo de Pedido Actualizado
- [ ] `HomeController` → redirige a login si no autenticado
- [ ] `OrderController` → usa cliente autenticado
- [ ] `OrderCreator` → acepta cliente pre-autenticado
- [ ] `home.blade.php` → checkout pre-relleno con datos del cliente
- [ ] `OrderTrackingController` → acceso por guard en lugar de sesión
- [ ] commit + push

## FASE D — Direcciones + Info de Entrega
- [ ] Modelo `ClienteDireccion`
- [ ] `DireccionController` (CRUD)
- [ ] Rutas de direcciones
- [ ] Vista `app/perfil/direcciones.blade.php`
- [ ] Checkout muestra direcciones guardadas
- [ ] Primer pedido solicita info de entrega completa
- [ ] commit + push

## FASE E — Perfil Completo Editable
- [ ] `PerfilController`
- [ ] Vista `app/perfil.blade.php` (5 bloques progresivos)
  - Datos personales (DNI, fecha nacimiento, sexo)
  - Datos de contacto
  - Cambio de contraseña
  - Direcciones
  - Preferencias (notificaciones, método de pago favorito)
- [ ] commit + push

## FASE F — Campos Automáticos del Sistema
- [ ] Observer `ClienteObserver` → genera `codigo_cliente`
- [ ] Observer `PedidoObserver` → actualiza `total_pedidos`, `total_gastado`, `ultimo_pedido_at`
- [ ] Login actualiza `ultimo_acceso`, `ip_ultimo_acceso`
- [ ] `PushSubscriptionController` → usa guard `cliente`
- [ ] Tests actualizados para nuevo flujo auth
- [ ] commit + push
