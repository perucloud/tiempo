# Checklist: Deploy

## Antes de desplegar

- [ ] `git status` limpio.
- [ ] Tests o checklist ejecutados.
- [ ] `.env` no versionado.
- [ ] Dependencias instalables.
- [ ] Migraciones revisadas.

## Produccion

- [ ] `APP_DEBUG=false`.
- [ ] HTTPS activo.
- [ ] Base de datos configurada.
- [ ] Backups definidos.
- [ ] Permisos de storage correctos.
- [ ] Logs revisables.

## Assets y PWA

- [ ] Assets compilados.
- [ ] Manifest revisado.
- [ ] Service worker revisado si aplica.
- [ ] Iconos correctos.

## Despues

- [ ] Smoke test.
- [ ] Login admin.
- [ ] Login por roles principales.
- [ ] Permisos de Negocio Afiliado, Repartidor y Cliente verificados.
- [ ] Flujo critico probado.
- [ ] Monitoreo inicial.
