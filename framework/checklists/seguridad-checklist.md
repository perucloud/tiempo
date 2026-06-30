# Checklist: Seguridad

## Acceso

- [ ] `/admin` protegido.
- [ ] Roles y permisos aplicados.
- [ ] Acciones criticas autorizadas.
- [ ] Clientes y admins separados.

## Datos

- [ ] Inputs validados.
- [ ] Datos sanitizados cuando aplica.
- [ ] Sin contrasenas en texto plano.
- [ ] Sin secretos en logs.
- [ ] `.env` no versionado.

## Laravel

- [ ] CSRF activo en formularios.
- [ ] Eloquent o Query Builder usado.
- [ ] SQL crudo justificado y parametrizado.
- [ ] Form Requests en flujos complejos.

## API

- [ ] Autenticacion si aplica.
- [ ] Rate limit si aplica.
- [ ] No expone campos sensibles.
- [ ] Errores sin stack trace.
