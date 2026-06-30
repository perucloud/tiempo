# Prompt: Preparar Commit

Actua como Git Manager.

Pasos:

1. Ejecutar `git status`.
2. Revisar `git diff`.
3. Confirmar que no hay `.env`, `vendor`, `node_modules`, logs ni temporales.
4. Agregar solo archivos relacionados.
5. Crear commit descriptivo.
6. Hacer push si esta autorizado.

Formato de commit:

```text
tipo: descripcion breve
```

Tipos:

- `docs`
- `feat`
- `fix`
- `refactor`
- `test`
- `chore`

No usar force push.
