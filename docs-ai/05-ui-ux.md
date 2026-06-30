# UI/UX

## Estilo general

TIEMPO debe tener un diseno moderno, limpio y profesional.

La interfaz debe transmitir rapidez, confianza y orden operativo.

## Dashboard `/admin`

El dashboard es desktop primero, pero debe tener una version responsive movil simplificada para administradores, operadores y duenos.

En desktop debe incluir:

- Sidebar claro.
- Topbar simple.
- Cards estadisticas.
- Tablas con busqueda.
- Filtros por estado, fecha y modulo.
- Acciones visibles.
- Formularios ordenados.
- Botones consistentes.
- Badges para estados.

El dashboard debe ser una herramienta de trabajo, no una landing decorativa.

## Interfaces por rol

- SuperAdmin/Admin: panel completo segun permisos.
- Operador: foco en pedidos, pagos, estados, repartidores y comunicacion.
- Negocio Afiliado: vista limitada a Mi Perfil, Mi Carta Digital, Productos, Categorias, Fotos, Horarios, Promociones e Informacion del Negocio.
- Repartidor: vista simple de pedidos asignados, ruta, cliente y estados.
- Cliente: experiencia exclusiva en `/app`.

La UI nunca debe mostrar al Negocio Afiliado opciones de clientes, pagos, repartidores, usuarios, configuracion, reportes generales ni otros negocios.

## Admin movil `/admin`

El admin movil debe permitir operar desde celular sin depender de una laptop.

Debe priorizar:

- Pedidos nuevos y en curso.
- Verificacion rapida de pagos.
- Cambio de estados.
- Asignacion o consulta de repartidores.
- Ventas rapidas del dia.

Reglas:

- No copiar tablas grandes de desktop.
- Usar cards compactas.
- Usar botones grandes.
- Usar navegacion simple.
- Mostrar solo acciones frecuentes.
- Mantener filtros rapidos por estado.
- Dejar reportes complejos solo para desktop.
- Evitar pantallas densas o formularios largos.

## Tablas

Toda tabla importante debe considerar:

- Busqueda.
- Filtros.
- Paginacion.
- Acciones por fila.
- Estados visuales.
- Empty state.

## Formularios

Reglas:

- Campos agrupados.
- Labels claros.
- Mensajes de error visibles.
- Botones principales y secundarios consistentes.
- Confirmacion para acciones criticas.

## Responsive

Responsive es obligatorio.

- `/admin`: optimizado para desktop, usable en tablet.
- `/admin` movil: optimizado para operacion rapida desde celular.
- `/app`: optimizado para movil.
- Landing `/`: responsive completo.

## App `/app`

La app movil debe ser:

- Mobile-first.
- Rapida.
- Simple.
- Facil de comprar.
- Con carrito accesible.
- Con estado del pedido visible.

## Reglas especificas para App Movil

`/app` es una aplicacion movil real, no una version responsive del dashboard ni una copia de la landing.

Reglas:

- Mobile First estricto.
- Disenada exclusivamente para telefonos moviles.
- Botones grandes para interaccion tactil.
- Navegacion inferior persistente cuando aporte claridad.
- Cards para negocios afiliados, productos, pedidos y promociones.
- Gestos simples: tocar, deslizar, volver, expandir.
- Animaciones suaves y discretas.
- Optimizada para uso con una sola mano.
- Flujo rapido: explorar, agregar, pagar y seguir pedido.
- Carrito siempre accesible.
- Experiencia tipo aplicacion nativa.
- Inspiracion de experiencia: Uber Eats, Rappi y PedidosYa.

Evitar:

- Tablas administrativas.
- Menus complejos de escritorio.
- Formularios largos.
- Contenido de marketing propio de la landing.
