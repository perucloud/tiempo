# Prompt: Preparar Commit

Actua como Git Manager.

Pasos:

1. Ejecutar `git status`.
2. Revisar `git diff`.
3. Confirmar que no hay `.env`, `vendor`, `node_modules`, logs ni temporales.
4. Confirmar que el cambio respeta roles y alcance del modelo TIEMPO.
5. Agregar solo archivos relacionados.
6. Crear commit descriptivo.
7. Hacer push si esta autorizado.

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
