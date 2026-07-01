# Master Roadmap TIEMPO Delivery

Este documento es la fuente oficial del estado del proyecto TIEMPO Delivery. Todo agente IA debe leerlo antes de proponer o ejecutar trabajo.

## 1. Vision General del Proyecto

### Objetivo

Construir desde cero una plataforma integral de delivery con Laravel, MySQL, Livewire, Bootstrap o Tailwind, PWA en `/app` y futura APK Android con Capacitor.

### Estado actual

- Documentacion base `docs-ai/00` a `docs-ai/14` creada.
- Repositorio Git inicial creado y documentacion base subida a remoto.
- Laravel instalado como base limpia.
- No existe codigo funcional de negocio.
- Entorno local configurado con MySQL, timezone, locale y variables base.
- Autenticacion base de `/admin` implementada y verificada.
- Dashboard administrativo base creado con layout desktop-first y responsive movil simplificado.
- Modelo relacional inicial de TIEMPO creado con migraciones, modelos, relaciones y seeders base.
- Landing publica en `/` creada y verificada.
- Base de API interna bajo `/api` creada y verificada.
- Diseno UX Mobile de `/app` definido antes del desarrollo.
- Base mobile-first de `/app` creada y verificada.
- Gestion de Usuarios base implementada en `/admin`.
- Categorias globales implementadas en `/admin`.
- Negocios afiliados implementados en `/admin`.
- Productos implementados en `/admin`.
- Clientes implementados en `/admin`.
- Carrito de compra implementado en `/app`.
- Pedidos implementados desde `/app` y gestion operativa en `/admin`.
- Pagos Yape/Plin implementados con revision operativa.
- La siguiente accion tecnica debe ser implementar Repartidores.

### Arquitectura

- `/`: landing page publica.
- `/admin`: dashboard administrativo para desktop y operacion movil simplificada.
- `/app`: app movil/PWA exclusiva para clientes, interfaz principal y exclusiva para telefonos moviles.
- `/api`: API interna JSON.

Las interfaces `/`, `/admin` y `/app` son independientes. Cada una tiene layout, navegacion, componentes y experiencia propios. `/admin` debe ser desktop-first con experiencia completa, pero tambien debe permitir al dueno u operador gestionar pedidos desde celular con una experiencia simplificada.

### Modelo de negocio

TIEMPO es una empresa de delivery, no un restaurante.

- TIEMPO administra pedidos, clientes, pagos, estados, repartidores y reportes generales.
- Los negocios afiliados solo administran su informacion, carta digital, productos, categorias, fotos, horarios y promociones.
- Los negocios afiliados no administran pedidos globales, clientes, pagos, reportes generales, repartidores, usuarios ni configuracion.
- Los productos publicados por negocios afiliados son usados por TIEMPO para vender a clientes.

Roles oficiales:

- SuperAdmin.
- Admin.
- Operador.
- Negocio Afiliado.
- Repartidor.
- Cliente.

Capas objetivo:

- Rutas separadas por superficie.
- Controladores delgados.
- Modelos Eloquent con relaciones claras.
- Servicios o Actions para logica de negocio.
- Blade y Livewire para interfaces.
- API Resources para respuestas JSON.
- Migraciones, seeders y factories para base de datos.

### Leyenda de estados

- ☐ Pendiente
- ◐ En desarrollo
- ☑ Finalizado

## 2. Fases del Proyecto

## FASE 00 - Documentacion IA

Estado: ☑ Finalizado

Objetivo: Definir las reglas oficiales para que agentes IA desarrollen TIEMPO con consistencia.

Descripcion: Crear y mantener documentos de contexto, arquitectura, desarrollo, Git, estandares, UI/UX, base de datos, API, PWA, seguridad, modulos, flujos, testing y roadmap.

Tareas:

- Crear documentos `docs-ai/00` a `docs-ai/14`.
- Crear este Master Roadmap.
- Mantener cada documento corto, claro y accionable.
- Versionar cambios de documentacion.

Dependencias:

- Ninguna.

Criterios de finalizacion:

- Todos los documentos `00` a `14` existen.
- El roadmap tiene fases, dependencias y reglas.
- Cambios documentales estan commiteados y subidos al remoto.

Resumen de trabajo realizado:

- Documentacion base creada y versionada.
- Master Roadmap creado en esta fase.
- Agentes IA y framework de trabajo creados y versionados.

## FASE 01 - Instalacion Laravel

Estado: ☑ Finalizado

Objetivo: Instalar un proyecto Laravel limpio en el repositorio.

Descripcion: Crear la base tecnica sin reutilizar codigo heredado.

Tareas:

- Proponer plan de archivos antes de instalar.
- Instalar Laravel limpio.
- Confirmar estructura base.
- Verificar que no se versionen `vendor`, `.env` ni temporales.

Dependencias:

- FASE 00.

Criterios de finalizacion:

- Laravel instalado correctamente.
- `php artisan` funciona.
- Proyecto arranca localmente.
- Commit y push realizados.

Resumen de trabajo realizado:

- Laravel instalado desde cero usando carpeta temporal para conservar `docs-ai`, `.agents` y `framework`.
- Laravel Framework 13.17.0 verificado con `php artisan --version`.
- `.env`, `vendor`, `node_modules` y `database/database.sqlite` verificados como no versionables.

## FASE 02 - Configuracion del entorno

Estado: ☑ Finalizado

Objetivo: Configurar entorno local, variables, base MySQL y herramientas base.

Descripcion: Preparar `.env`, conexion MySQL, Vite, timezone, locale y configuraciones iniciales.

Tareas:

- Configurar `.env.example`.
- Configurar MySQL.
- Definir `APP_NAME`, timezone y locale.
- Validar `php artisan migrate` cuando existan migraciones.

Dependencias:

- FASE 01.

Criterios de finalizacion:

- Entorno local documentado.
- Conexion MySQL verificada.
- No se sube `.env`.
- Commit y push realizados.

Resumen de trabajo realizado:

- `.env.example` configurado para TIEMPO Delivery, MySQL, `America/Lima` y locale `es`.
- `.env` local configurado con credenciales MySQL entregadas por el responsable del proyecto.
- Base MySQL `tiempo` creada/verificada con `utf8mb4_unicode_ci`.
- Migraciones base Laravel ejecutadas correctamente en MySQL.
- `config/app.php` actualizado para leer `APP_TIMEZONE`.
- `php artisan test` ejecutado correctamente.

## FASE 03 - Autenticacion

Estado: ☑ Finalizado

Objetivo: Implementar autenticacion base para admin y preparar separacion con clientes.

Descripcion: Proteger `/admin` y dejar definidos flujos separados para usuarios administrativos, negocios afiliados, repartidores y clientes.

Tareas:

- Elegir stack de autenticacion Laravel.
- Crear login admin.
- Proteger rutas `/admin`.
- Definir hashing y sesiones seguras.
- Preparar roles/permisos.
- Definir roles oficiales: SuperAdmin, Admin, Operador, Negocio Afiliado, Repartidor y Cliente.

Dependencias:

- FASE 02.

Criterios de finalizacion:

- Admin puede iniciar y cerrar sesion.
- `/admin` bloquea usuarios no autenticados.
- Negocio Afiliado, Repartidor y Cliente quedan separados por permisos y alcance.
- Login probado.
- Commit y push realizados.

Resumen de trabajo realizado:

- Login administrativo creado bajo `/admin/login`.
- Rutas `/admin` protegidas con middleware `auth` y `admin.access`.
- Roles oficiales preparados en el modelo `User`.
- Campos `role` y `status` agregados a `users`.
- Seeder local preparado para crear SuperAdmin mediante variables `.env` no versionadas.
- Tests de autenticacion admin creados y ejecutados correctamente.

## FASE 04 - Dashboard Base

Estado: ☑ Finalizado

Objetivo: Crear layout base del dashboard administrativo.

Descripcion: Construir interfaz desktop-first con sidebar, topbar, cards, tablas base y componentes reutilizables, incluyendo una version responsive movil simplificada para operacion rapida.

Tareas:

- Crear layout admin.
- Crear sidebar y topbar.
- Crear dashboard inicial.
- Crear componentes UI base.
- Asegurar responsive minimo.
- Definir patrones admin movil con cards, botones grandes y navegacion simple.
- Priorizar en admin movil pedidos, pagos, estados, repartidores y ventas rapidas.

Dependencias:

- FASE 03.

Criterios de finalizacion:

- `/admin` carga con layout.
- Sidebar muestra modulos previstos.
- UI cumple reglas de `05-ui-ux.md`.
- Admin movil permite operar lo urgente sin copiar tablas grandes de desktop.
- Reportes complejos quedan reservados para desktop.
- Commit y push realizados.

Resumen de trabajo realizado:

- Layout admin creado con sidebar, topbar, usuario autenticado y logout.
- Dashboard inicial creado con cards estadisticas, modulos previstos y tabla base desktop.
- Vista movil simplificada creada para pedidos, pagos, estados, repartidores y ventas rapidas.
- CSS admin organizado en `public/css/admin.css` sin dependencias adicionales.
- Tests actualizados para validar que el dashboard muestra modulos principales.

## FASE 05 - Base de Datos

Estado: ☑ Finalizado

Objetivo: Crear modelo relacional inicial de TIEMPO.

Descripcion: Implementar migraciones, relaciones, seeders y factories principales.

Tareas:

- Crear tablas iniciales.
- Definir foreign keys e indices.
- Crear modelos Eloquent.
- Crear seeders base.
- Definir estados controlados.
- Modelar negocios afiliados y permisos por rol.
- Separar clientes, repartidores y usuarios administrativos segun alcance.

Dependencias:

- FASE 02.
- FASE 03.

Criterios de finalizacion:

- Migraciones corren desde cero.
- Relaciones Eloquent principales existen.
- Seeders base funcionan.
- Commit y push realizados.

Resumen de trabajo realizado:

- Migracion central creada para roles, permisos, categorias, negocios afiliados, productos, clientes, repartidores, pedidos, detalle de pedidos, pagos e historial de estados.
- Modelos Eloquent principales creados con relaciones iniciales.
- Estados controlados definidos en modelos clave.
- Factories principales creadas para pruebas y futuros modulos.
- Seeders base creados para roles oficiales, permisos iniciales y categorias.
- Migraciones y seeders ejecutados correctamente en MySQL local.
- Tests de esquema y relaciones agregados y ejecutados correctamente.

## FASE 06 - Landing Page

Estado: ☑ Finalizado

Objetivo: Crear landing publica en `/`.

Descripcion: Presentar TIEMPO, beneficios y accesos principales.

Tareas:

- Crear ruta publica `/`.
- Crear vista responsive.
- Agregar CTA y secciones basicas.
- Enlazar acceso a `/app` y `/admin` si aplica.
- Captar clientes y negocios afiliados.

Dependencias:

- FASE 01.
- FASE 02.

Criterios de finalizacion:

- Landing carga en desktop y mobile.
- No mezcla logica de negocio.
- Commit y push realizados.

Resumen de trabajo realizado:

- Ruta publica `/` configurada con nombre `home`.
- Layout web independiente creado para la landing publica.
- Landing responsive creada para captacion de clientes y negocios afiliados.
- CTAs agregados para app de clientes, afiliacion de negocios y acceso admin.
- Estilos publicos organizados en `public/css/web.css`.
- Asset visual local creado para el hero de la landing.
- Tests de landing agregados y ejecutados correctamente.

## FASE 07 - API

Estado: ☑ Finalizado

Objetivo: Crear base de API interna bajo `/api`.

Descripcion: Definir respuestas JSON uniformes, middleware, Resources y versionado inicial si aplica.

Tareas:

- Crear estructura de controladores API.
- Definir formato JSON.
- Crear endpoints health o base.
- Configurar autenticacion cuando aplique.
- Documentar endpoints iniciales.
- Definir alcance API por consumidor: cliente, repartidor, negocio afiliado y admin/operador.

Dependencias:

- FASE 02.
- FASE 05.

Criterios de finalizacion:

- `/api` responde con formato consistente.
- Errores usan codigos HTTP correctos.
- No expone datos sensibles.
- No expone datos globales a negocios afiliados ni pedidos no asignados a repartidores.
- Commit y push realizados.

Resumen de trabajo realizado:

- Archivo `routes/api.php` creado y registrado en Laravel.
- Endpoint base `/api/v1/health` creado.
- Controlador API separado en `app/Http/Controllers/Api`.
- Respuesta JSON uniforme creada con `data`, `message`, `errors` y `meta`.
- Manejo JSON uniforme para rutas API no encontradas.
- Tests de contrato API agregados y ejecutados correctamente.

## FASE 07.5 - Diseno UX Mobile

Estado: ☑ Finalizado

Objetivo: Disenar completamente la experiencia movil antes de comenzar el desarrollo de `/app`.

Descripcion: Definir la filosofia, navegacion, componentes, flujo de compra, carrito, formularios, estados visuales, comportamiento PWA y criterios para futura APK.

Tareas:

- Crear guia oficial de App Movil.
- Definir navegacion inferior.
- Definir componentes mobile: cards, botones, formularios, carrito y estados.
- Definir flujo de compra inspirado en apps tipo Uber Eats, Rappi o PedidosYa.
- Validar accesibilidad, uso con una mano y rendimiento.
- Alinear PWA y futura APK con Capacitor.

Dependencias:

- FASE 00.
- FASE 06.
- FASE 07.

Criterios de finalizacion:

- `docs-ai/14-mobile-app-guidelines.md` existe.
- La experiencia `/app` queda definida como interfaz independiente.
- El equipo entiende que `/app` no reutiliza dashboard ni landing.
- Commit y push realizados.

Resumen de trabajo realizado:

- Guia oficial mobile ampliada con mapa de pantallas base.
- Navegacion inferior definida para Inicio, Buscar, Carrito, Pedidos y Perfil.
- Componentes por pantalla definidos para inicio, negocio, producto, carrito y seguimiento.
- Reglas de botones, formularios, carrito, checkout y estados de pedido definidas.
- Criterios PWA/APK, rendimiento y accesibilidad alineados antes de implementar `/app`.

## FASE 08 - App movil / PWA

Estado: ☑ Finalizado

Objetivo: Crear base mobile-first en `/app`.

Descripcion: Implementar estructura inicial de la PWA para clientes siguiendo la guia oficial mobile.

Tareas:

- Crear rutas `/app`.
- Crear layout mobile-first.
- Crear navegacion base.
- Preparar manifest.
- Definir estrategia de service worker.

Dependencias:

- FASE 02.
- FASE 06.
- FASE 07.
- FASE 07.5.

Criterios de finalizacion:

- `/app` carga en celular.
- Layout mobile-first funcional.
- PWA preparada sin cachear datos sensibles.
- Commit y push realizados.

Resumen de trabajo realizado:

- Archivo `routes/app.php` creado y registrado desde rutas web.
- Controlador `App\HomeController` creado para la entrada `/app`.
- Layout exclusivo `resources/views/layouts/app-mobile.blade.php` creado.
- Vista inicial mobile-first `resources/views/app/home.blade.php` creada.
- Navegacion inferior, buscador, categorias, negocios afiliados, productos, carrito, seguimiento y perfil definidos como base visual.
- Assets propios creados en `public/css/app-mobile.css` y `public/js/app-mobile.js`.
- Manifest, icono y service worker inicial creados bajo `public/app`.
- Service worker preparado sin cachear datos sensibles.
- Pruebas de `/app`, manifest y service worker agregadas y ejecutadas correctamente.

## FASE 09 - Gestion de Usuarios

Estado: ☑ Finalizado

Objetivo: Administrar usuarios operadores/admin.

Descripcion: Crear CRUD de usuarios, roles y permisos para SuperAdmin, Admin, Operador, Negocio Afiliado, Repartidor y Cliente segun alcance.

Tareas:

- Crear modulo usuarios en `/admin`.
- Crear roles.
- Crear permisos por modulo.
- Validar crear, editar, activar/desactivar.
- Proteger acciones criticas.
- Restringir Negocio Afiliado a su propio negocio/carta.
- Restringir Repartidor a pedidos asignados.
- Restringir Cliente a `/app`.

Dependencias:

- FASE 03.
- FASE 04.
- FASE 05.

Criterios de finalizacion:

- CRUD usuarios funciona.
- Permisos bloquean acciones no autorizadas.
- Roles oficiales estan sembrados o documentados.
- Testing de login y permisos completado.
- Commit y push realizados.

Resumen de trabajo realizado:

- Modulo `admin/users` creado para listar, crear y editar usuarios.
- Middleware especifico agregado para limitar Gestion de Usuarios a SuperAdmin y Admin.
- Validaciones creadas con Form Requests para alta y actualizacion.
- Roles y estados oficiales usados desde el modelo `User`.
- Navegacion administrativa centralizada para activar el modulo Usuarios.
- UI de listado y formulario agregada al dashboard.
- Tests de acceso, creacion, actualizacion y bloqueo por rol agregados y ejecutados correctamente.

## FASE 10 - Categorias

Estado: ☑ Finalizado

Objetivo: Gestionar categorias de comidas, bebidas y futuras categorias.

Descripcion: Crear CRUD administrativo para categorias.

Tareas:

- Crear migracion/modelo si falta.
- Crear CRUD en `/admin`.
- Agregar filtros y estados.
- Validar nombres duplicados.

Dependencias:

- FASE 04.
- FASE 05.
- FASE 09.

Criterios de finalizacion:

- Categorias se crean, editan y desactivan.
- UI cumple reglas de tabla/formulario.
- Commit y push realizados.

Resumen de trabajo realizado:

- Modulo `admin/categories` creado para listar, crear, editar y desactivar categorias globales.
- Middleware especifico agregado para permitir gestion a SuperAdmin, Admin y Operador.
- Negocio Afiliado queda bloqueado para categorias globales de TIEMPO.
- Validaciones creadas con Form Requests.
- Filtros por busqueda, tipo y estado agregados.
- Slug unico generado automaticamente desde el nombre.
- UI de listado, filtros y formulario agregada al dashboard.
- Tests de acceso, creacion, edicion, filtros y soft delete agregados y ejecutados correctamente.

## FASE 11 - Negocios afiliados

Estado: ☑ Finalizado

Objetivo: Gestionar negocios afiliados.

Descripcion: Crear CRUD de negocios afiliados con datos generales, horarios, estado e informacion comercial.

Tareas:

- Crear modelo y migraciones necesarias.
- Crear CRUD admin.
- Gestionar abierto/cerrado.
- Relacionar con productos.
- Permitir acceso limitado del Negocio Afiliado a su propia informacion.

Dependencias:

- FASE 05.
- FASE 09.
- FASE 10.

Criterios de finalizacion:

- Negocios afiliados se administran desde `/admin`.
- Estado de disponibilidad funciona.
- Negocio Afiliado no accede a pedidos/clientes/pagos/repartidores globales.
- Commit y push realizados.

Resumen de trabajo realizado:

- Modulo `admin/businesses` creado para listar, crear, editar y desactivar negocios afiliados.
- Middleware especifico agregado para permitir gestion a SuperAdmin, Admin y Operador.
- Negocio Afiliado queda bloqueado para este CRUD global.
- Validaciones creadas con Form Requests.
- Filtros por busqueda, tipo de negocio y estado agregados.
- Estado activo/inactivo y disponibilidad abierto/cerrado implementados.
- Horarios simples guardados como estructura JSON.
- Slug unico generado automaticamente desde el nombre comercial.
- UI de listado, filtros y formulario agregada al dashboard.
- Tests de acceso, creacion, edicion, filtros, duplicados y soft delete agregados y ejecutados correctamente.

## FASE 12 - Productos

Estado: ☑ Finalizado

Objetivo: Gestionar carta/productos por negocio afiliado.

Descripcion: Crear CRUD de productos con precio, categoria, negocio afiliado y disponibilidad.

Tareas:

- Crear modelo y migraciones necesarias.
- Crear CRUD admin.
- Asociar producto a negocio afiliado y categoria.
- Gestionar disponibilidad.
- Preparar imagen si aplica.

Dependencias:

- FASE 10.
- FASE 11.

Criterios de finalizacion:

- Productos se administran correctamente.
- Catalogo puede consumir productos activos.
- Commit y push realizados.

Resumen de trabajo realizado:

- Modulo `admin/products` creado para listar, crear, editar y desactivar productos.
- Middleware especifico agregado para permitir gestion a SuperAdmin, Admin y Operador.
- Negocio Afiliado queda bloqueado para este CRUD global.
- Validaciones creadas con Form Requests.
- Productos asociados a negocio afiliado y categoria.
- Disponibilidad, estado, precio y precio promocional implementados.
- Slug unico generado por negocio afiliado.
- UI de listado, filtros y formulario agregada al dashboard.
- Tests de acceso, creacion, edicion, filtros, validacion de precio promocional y soft delete agregados y ejecutados correctamente.

## FASE 13 - Clientes

Estado: ☑ Finalizado

Objetivo: Gestionar clientes y sus datos.

Descripcion: Crear registro, consulta, direcciones e historial base del cliente.

Tareas:

- Crear modelo cliente.
- Crear direcciones.
- Crear consulta admin.
- Preparar autenticacion cliente para `/app`.

Dependencias:

- FASE 05.
- FASE 08.
- FASE 09.

Criterios de finalizacion:

- Clientes pueden existir con datos base.
- Direccion de entrega queda preparada para FASE 14 - Carrito.
- Admin puede consultar clientes.
- Commit y push realizados.

Resumen de trabajo realizado:

- Modulo `admin/clients` creado para listar, crear, editar y desactivar clientes.
- Middleware especifico agregado para permitir gestion a SuperAdmin, Admin y Operador.
- Negocio Afiliado queda bloqueado para consultar clientes globales.
- Validaciones creadas con Form Requests.
- Filtros por busqueda y estado agregados.
- Telefono unico activo validado.
- UI de listado, filtros y formulario agregada al dashboard.
- Direcciones quedan pendientes para el flujo de carrito/checkout, donde se modelara la direccion de entrega.
- Tests de acceso, creacion, edicion, filtros, duplicados y soft delete agregados y ejecutados correctamente.

## FASE 14 - Carrito

Estado: ☑ Finalizado

Objetivo: Implementar carrito de compra en `/app`.

Descripcion: Permitir agregar productos, modificar cantidades y preparar checkout.

Tareas:

- Crear UI de carrito.
- Manejar cantidades.
- Calcular subtotal.
- Validar disponibilidad.
- Preparar datos para pedido.

Dependencias:

- FASE 08.
- FASE 12.
- FASE 13.

Criterios de finalizacion:

- Cliente puede armar carrito.
- Carrito mantiene datos necesarios.
- Commit y push realizados.

Resumen de trabajo realizado:

- Carrito en sesion creado para `/app`.
- Cliente puede agregar productos activos y disponibles.
- Cantidades pueden aumentar, disminuir o retirar producto.
- Carrito valida un solo negocio afiliado por compra.
- Subtotal, delivery y total se calculan en la vista mobile.
- Direccion de entrega queda preparada para el checkout.
- Cliente puede vaciar carrito.
- Catalogo mobile consume categorias, negocios y productos activos reales.
- Tests de carrito, disponibilidad, direccion, catalogo y rutas agregados y ejecutados correctamente.

## FASE 15 - Pedidos

Estado: ☑ Finalizado

Objetivo: Crear gestion completa del pedido.

Descripcion: Implementar creacion, detalle, historial, estados y gestion operativa.

Tareas:

- Crear pedido desde `/app`.
- Crear detalle en `/admin`.
- Implementar estados.
- Registrar historial.
- Permitir cancelacion con motivo.

Dependencias:

- FASE 14.
- FASE 05.
- FASE 09.

Criterios de finalizacion:

- Pedido se crea desde carrito.
- Admin puede ver y cambiar estados.
- Historial queda registrado.
- Commit y push realizados.

Resumen de trabajo realizado:

- Creacion de pedido desde carrito implementada en `/app`.
- Cliente se crea o actualiza por telefono al confirmar pedido.
- Pedido, detalle e historial inicial se crean en transaccion.
- Carrito se limpia despues de crear pedido.
- Direccion de entrega es obligatoria para crear pedido.
- Vista admin `admin/orders` agregada para listar y filtrar pedidos.
- Vista admin de detalle permite revisar resumen, productos e historial.
- Cambio de estado operativo implementado con auditoria en `pedido_estados`.
- Negocio Afiliado queda bloqueado para pedidos globales.
- Tests de creacion, validacion, listado, filtros y cambio de estado agregados y ejecutados correctamente.

## FASE 16 - Pagos

Estado: ☑ Finalizado

Objetivo: Implementar verificacion de pagos Yape/Plin.

Descripcion: Permitir voucher, revision, aprobacion, rechazo y auditoria de pagos.

Tareas:

- Crear modelo pago.
- Subir voucher desde `/app`.
- Revisar voucher desde `/admin`.
- Aprobar/rechazar pago.
- Vincular pago con avance de pedido.

Dependencias:

- FASE 15.
- FASE 09.

Criterios de finalizacion:

- Voucher se sube y valida.
- Operador aprueba o rechaza.
- Estados de pago impactan pedido.
- Commit y push realizados.

Resumen de trabajo realizado:

- Registro de pago desde `/app` implementado por codigo de pedido.
- Metodos Yape y Plin definidos.
- Voucher se registra como URL y codigo de operacion.
- Pedido pasa a `pago_en_revision` al registrar pago.
- Vista admin `admin/payments` agregada para listar y filtrar pagos.
- Vista admin de revision permite aprobar o rechazar.
- Aprobacion actualiza pago, confirma pedido y registra historial.
- Rechazo actualiza pago, retorna pedido a pendiente y registra historial.
- Negocio Afiliado queda bloqueado para pagos globales.
- Tests de registro, filtros, aprobacion, rechazo y bloqueo por rol agregados y ejecutados correctamente.

## FASE 17 - Repartidores

Estado: ☐ Pendiente

Objetivo: Gestionar repartidores y asignaciones.

Descripcion: Crear disponibilidad, asignacion a pedidos y flujo de entrega.

Tareas:

- Crear CRUD repartidores.
- Gestionar disponibilidad.
- Asignar pedido.
- Cambiar estado en camino/entregado.
- Registrar incidencias.

Dependencias:

- FASE 15.
- FASE 16.

Criterios de finalizacion:

- Repartidor puede ser asignado.
- Pedido avanza a entrega.
- Historial registra cambios.
- Commit y push realizados.

## FASE 18 - Reportes

Estado: ☐ Pendiente

Objetivo: Crear reportes administrativos.

Descripcion: Mostrar ventas, pedidos, pagos, negocios afiliados y repartidores.

Tareas:

- Crear reportes por fecha.
- Crear reportes de ventas.
- Crear reportes de pagos.
- Crear indicadores de repartidores.
- Agregar filtros.

Dependencias:

- FASE 15.
- FASE 16.
- FASE 17.

Criterios de finalizacion:

- Reportes clave cargan en `/admin`.
- Filtros funcionan.
- Datos son consistentes.
- Commit y push realizados.

## FASE 19 - Notificaciones

Estado: ☐ Pendiente

Objetivo: Notificar eventos importantes a clientes y operadores.

Descripcion: Definir y crear notificaciones para estados del pedido, pagos e incidencias.

Tareas:

- Definir canales iniciales.
- Notificar pago aprobado/rechazado.
- Notificar estado del pedido.
- Preparar base para push futuro.

Dependencias:

- FASE 15.
- FASE 16.
- FASE 08.

Criterios de finalizacion:

- Eventos principales generan notificacion.
- No se exponen datos sensibles.
- Commit y push realizados.

## FASE 20 - Configuracion

Estado: ☐ Pendiente

Objetivo: Gestionar datos generales del sistema.

Descripcion: Crear modulo de configuracion para nombre, contacto, zonas, tarifas y parametros operativos.

Tareas:

- Crear configuraciones generales.
- Crear zonas y tarifas.
- Proteger acceso por permiso.
- Auditar cambios.

Dependencias:

- FASE 09.
- FASE 05.

Criterios de finalizacion:

- Configuracion se administra desde `/admin`.
- Cambios criticos quedan auditados.
- Commit y push realizados.

## FASE 21 - Testing

Estado: ☐ Pendiente

Objetivo: Validar funcionalidad critica.

Descripcion: Ejecutar checklist manual y pruebas automatizadas cuando existan.

Tareas:

- Probar login.
- Probar CRUDs.
- Probar pedido completo.
- Probar voucher y pagos.
- Probar asignacion de repartidor.
- Probar responsive y PWA.
- Probar API.

Dependencias:

- FASE 15.
- FASE 16.
- FASE 17.
- FASE 20.

Criterios de finalizacion:

- Checklist `12-testing-checklist.md` completado.
- Errores criticos corregidos.
- Commit y push realizados.

## FASE 22 - Optimizacion

Estado: ☐ Pendiente

Objetivo: Mejorar rendimiento, UX y mantenibilidad.

Descripcion: Optimizar consultas, assets, componentes, validaciones y flujos lentos.

Tareas:

- Revisar consultas N+1.
- Agregar indices faltantes.
- Optimizar assets.
- Mejorar cargas en `/app`.
- Revisar experiencia de admin.

Dependencias:

- FASE 21.

Criterios de finalizacion:

- Flujos principales son rapidos.
- No hay problemas evidentes de rendimiento.
- Commit y push realizados.

## FASE 23 - APK Android

Estado: ☐ Pendiente

Objetivo: Preparar APK Android con Capacitor.

Descripcion: Empaquetar la PWA mobile usando Capacitor cuando `/app` este estable.

Tareas:

- Instalar/configurar Capacitor cuando sea autorizado.
- Configurar iconos y splash.
- Validar permisos Android.
- Generar APK de prueba.

Dependencias:

- FASE 08.
- FASE 21.
- FASE 22.

Criterios de finalizacion:

- APK de prueba instala y carga `/app`.
- Solo se recompila por cambios nativos necesarios.
- Commit y push realizados.

## FASE 24 - Publicacion Play Store

Estado: ☐ Pendiente

Objetivo: Preparar publicacion Android.

Descripcion: Completar requisitos de Play Store, ficha, politicas y build firmada.

Tareas:

- Preparar build release.
- Preparar iconos, capturas y descripcion.
- Revisar politicas de privacidad.
- Publicar prueba interna.

Dependencias:

- FASE 23.

Criterios de finalizacion:

- App aceptada en canal de prueba o produccion segun decision.
- Documentacion de publicacion registrada.
- Commit y push realizados.

## FASE 25 - Produccion

Estado: ☐ Pendiente

Objetivo: Desplegar TIEMPO en entorno productivo.

Descripcion: Configurar servidor, dominio, HTTPS, base de datos, backups y variables.

Tareas:

- Configurar hosting/servidor.
- Configurar HTTPS.
- Configurar `.env` productivo.
- Ejecutar migraciones.
- Configurar backups.
- Validar flujos criticos.

Dependencias:

- FASE 21.
- FASE 22.

Criterios de finalizacion:

- Sistema operativo en produccion.
- `APP_DEBUG=false`.
- Backups definidos.
- Commit y push de ajustes no sensibles realizados.

## FASE 26 - Mantenimiento

Estado: ☐ Pendiente

Objetivo: Mantener, corregir y evolucionar TIEMPO.

Descripcion: Atender bugs, mejoras, soporte, seguridad y nuevas funcionalidades.

Tareas:

- Monitorear errores.
- Revisar logs.
- Corregir bugs.
- Planificar mejoras.
- Mantener dependencias.
- Actualizar documentacion.

Dependencias:

- FASE 25.

Criterios de finalizacion:

- Fase continua; se considera saludable si hay seguimiento, backups, documentacion y releases controlados.

## 3. Reglas del Roadmap

Despues de finalizar cada fase, todo agente IA debe:

- Actualizar el estado de la fase en este documento.
- Registrar un resumen del trabajo realizado.
- Crear un commit Git descriptivo.
- Hacer push al repositorio remoto.
- Proponer automaticamente la siguiente fase.
- Nunca saltar una fase sin autorizacion.

Reglas adicionales:

- Este documento es el director del proyecto.
- Siempre leer `docs-ai/00` a `docs-ai/14` antes de trabajar.
- No instalar dependencias sin justificar.
- No modificar archivos globales sin explicar impacto.
- No generar codigo de aplicacion antes de planificar archivos.
- No usar force push.
- No versionar `.env`, `vendor`, `node_modules`, logs ni temporales.

## Estado de siguiente fase propuesta

Siguiente fase sugerida: FASE 17 - Repartidores.

Antes de iniciar FASE 17, el agente debe proponer plan de archivos para gestion de repartidores, disponibilidad, asignacion a pedidos, estados de ruta y restricciones para que cada repartidor solo vea pedidos asignados.
