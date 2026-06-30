# Contexto TIEMPO

## Nombre del proyecto

TIEMPO Delivery.

## Objetivo general

Desarrollar desde cero una plataforma integral de delivery para clientes, negocios afiliados, repartidores y operadores administrativos.

## Modelo de negocio

TIEMPO no es un restaurante. TIEMPO es una empresa de delivery.

Los restaurantes, cafeterias, pollerias, pizzerias, licorerias, bodegas, farmacias y otros comercios son negocios afiliados a la plataforma.

TIEMPO administra la operacion completa del delivery:

- Pedidos.
- Clientes.
- Pagos.
- Verificacion de comprobantes.
- Estados del pedido.
- Asignacion de repartidores.
- Comunicacion operativa.
- Reportes generales.

El negocio afiliado no administra pedidos globales, clientes, pagos, repartidores ni reportes generales. Solo administra la informacion de su propio negocio, carta, productos, categorias, fotos, horarios y promociones.

## Rutas principales

- `/`: landing page publica.
- `/admin`: dashboard administrativo.
- `/app`: app movil/PWA para clientes.
- `/api`: API interna.

## Filosofia de Interfaces

TIEMPO tendra tres interfaces completamente independientes:

- `/`: Landing Page. Interfaz publica orientada a escritorio, marketing, SEO, informacion institucional y captacion de clientes y negocios afiliados.
- `/admin`: Dashboard Administrativo. Interfaz para administradores, operadores y duenos. Sera desktop-first con experiencia completa, pero tambien tendra una version responsive movil simplificada para operar pedidos, pagos, estados, repartidores y ventas rapidas desde celular.
- `/app`: App Movil. Interfaz principal exclusiva para clientes, disenada exclusivamente para telefonos moviles. No es una version responsive del dashboard ni una copia de la landing. Debe sentirse como una app nativa tipo Uber Eats, Rappi o PedidosYa.

Cada interfaz tendra layout, navegacion, componentes y experiencia de usuario propios.

Regla clave: el dueno u operador debe poder gestionar lo urgente desde `/admin` en celular sin depender de una laptop. La app `/app` nunca se usara para administracion.

## Stack tecnologico

- Laravel
- MySQL
- Livewire
- Bootstrap o Tailwind
- PWA
- Capacitor para futura APK

## Modulos principales

- Pedidos
- Clientes
- Categorias
- Negocios afiliados
- Productos
- Repartidores
- Pagos
- Reportes
- Usuarios
- Configuracion

## Roles oficiales

- SuperAdmin: control absoluto del sistema, usuarios, permisos y configuracion.
- Admin: control administrativo segun permisos otorgados por SuperAdmin.
- Operador: gestiona pedidos, pagos, estados, repartidores y comunicacion operativa.
- Negocio Afiliado: gestiona solo su perfil, carta digital, productos, categorias, fotos, horarios, promociones e informacion del negocio.
- Repartidor: accede solo a pedidos asignados, ruta, cliente y estados del pedido.
- Cliente: compra desde `/app`, sube comprobantes, consulta historial, hace seguimiento y gestiona su perfil.

## Reglas generales del proyecto

- Desarrollar desde cero en Laravel.
- No reutilizar codigo, rutas, layouts, estilos ni modulos heredados.
- Trabajar por modulos.
- Antes de programar, proponer plan de archivos.
- No modificar archivos globales sin autorizacion.
- Mantener `/admin`, `/app` y `/api` claramente separados.
