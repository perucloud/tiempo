# Checklist: Modulo CRUD

## Arquitectura

- [ ] Plan de archivos aprobado.
- [ ] Rutas bajo prefijo correcto.
- [ ] Controlador delgado o Livewire bien delimitado.
- [ ] Modelo con relaciones.
- [ ] Migracion revisada.
- [ ] Roles y alcance definidos.

## Funcionalidad

- [ ] Listado.
- [ ] Crear.
- [ ] Ver detalle si aplica.
- [ ] Editar.
- [ ] Desactivar/eliminar segun regla.
- [ ] Busqueda, filtros y paginacion si aplica.

## Seguridad

- [ ] Validaciones.
- [ ] CSRF en formularios.
- [ ] Permisos.
- [ ] Sin SQL en vistas.
- [ ] Negocio Afiliado limitado a datos propios.
- [ ] Repartidor limitado a pedidos asignados.
- [ ] Cliente limitado a `/app` y sus datos.

## Cierre

- [ ] Pruebas manuales.
- [ ] Responsive.
- [ ] `git diff` revisado.
- [ ] Commit descriptivo.
