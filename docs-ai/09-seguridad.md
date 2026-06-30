# Seguridad

## Autenticacion

La autenticacion es obligatoria en `/admin`.

Clientes de `/app` y usuarios administrativos deben tener accesos separados.

## Roles y permisos

Implementar roles y permisos para controlar:

- Modulos visibles.
- Acciones permitidas.
- Operaciones criticas.

Debe existir control de acceso por modulo.

## Formularios

- Usar CSRF en formularios.
- Validar inputs.
- Sanitizar datos cuando aplique.
- Mostrar errores claros.

## Base de datos

Proteger contra SQL Injection usando:

- Eloquent.
- Query Builder.
- Parametros enlazados si se usa SQL crudo.

No escribir SQL directo en vistas.

## Contrasenas

- No guardar contrasenas en texto plano.
- Usar hashing Laravel.
- No exponer hashes.
- No registrar contrasenas en logs.

## Datos sensibles

No exponer:

- `.env`
- Tokens
- Logs
- Credenciales
- Datos de pago sensibles
- Informacion privada innecesaria

## Acciones criticas

Requieren permiso y auditoria:

- Cambiar estado de pedido.
- Aprobar o rechazar pago.
- Asignar repartidor.
- Cancelar pedido.
- Crear o desactivar usuarios.
