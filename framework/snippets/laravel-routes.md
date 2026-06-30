# Snippet: Rutas Laravel

Separacion recomendada:

- `routes/web.php`: landing y paginas publicas.
- `routes/admin.php`: dashboard bajo `/admin`.
- `routes/app.php`: PWA bajo `/app`.
- `routes/api.php`: endpoints JSON bajo `/api`.

Convenciones:

- Nombres admin: `admin.modulo.accion`.
- Nombres app: `app.recurso.accion`.
- Nombres API: `api.recurso.accion`.
- Nombres afiliado si aplica: `affiliate.modulo.accion`.

Reglas:

- `/admin` siempre con middleware de autenticacion.
- `/admin` separa permisos de SuperAdmin, Admin, Operador, Negocio Afiliado y Repartidor.
- `/app` mobile-first y exclusivo para clientes.
- `/api` responde JSON.
- No mezclar HTML y JSON en la misma ruta.
