# Git Workflow

## Control de versiones

Git es el control de versiones oficial del proyecto TIEMPO.

## Antes de iniciar

Ejecutar:

```text
git status
```

Revisar cambios existentes antes de modificar archivos.

## Durante el desarrollo

Despues de cada avance funcional:

```text
git diff
git add .
git commit -m "tipo: descripcion clara"
```

Usar commits pequenos, descriptivos y relacionados con una sola tarea.

## Mensajes de commit

Formato recomendado:

```text
docs: actualizar guias del proyecto
feat: crear modulo de pedidos
fix: corregir validacion de pago
```

## Archivos que no se deben subir

- `.env`
- `vendor`
- `node_modules`
- Archivos temporales
- Logs
- Backups locales
- Credenciales
- Dumps con datos reales

## Push remoto

Preparar push al repositorio remoto cuando el avance este revisado.

Antes de push:

- Revisar `git status`.
- Revisar commits.
- Confirmar que no hay secretos.
- Confirmar que no hay archivos temporales.

## Reglas de seguridad Git

- No usar force push.
- No borrar historial sin autorizacion.
- No revertir cambios de otra persona sin confirmacion.
- No hacer commits gigantes con cambios no relacionados.
