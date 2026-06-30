# Reglas de Desarrollo

## Antes de programar

Siempre proponer un plan de archivos antes de crear o modificar codigo de aplicacion.

El plan debe indicar:

- Archivos a crear.
- Archivos a modificar.
- Rutas afectadas.
- Migraciones necesarias.
- Componentes Livewire involucrados.
- Riesgos o decisiones pendientes.

## Trabajo por modulos

Trabajar por modulos independientes:

- Pedidos
- Clientes
- Categorias
- Restaurantes
- Productos
- Repartidores
- Pagos
- Reportes
- Usuarios
- Configuracion

No mezclar cambios de varios modulos salvo que sea necesario y autorizado.

## Archivos globales

No tocar archivos globales sin autorizacion o sin explicar impacto.

Ejemplos:

- Configuracion Laravel.
- Bootstrap de rutas.
- Layouts base.
- Middleware global.
- Configuracion de Vite.

## Calidad del cambio

- No crear codigo duplicado.
- No hacer cambios masivos sin permiso.
- Mantener cambios pequenos y revisables.
- Usar convenciones Laravel.
- Siempre explicar que archivos se crearán o modificarán.
- No instalar dependencias sin justificar.
