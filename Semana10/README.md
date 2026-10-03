# Semana 10 — Validación y Form Requests

Esta entrega queda aislada en `Semana10/taskboard/` y continúa Semana 9. Jueves aplica validación inline, errores visibles y la regla `exists`; viernes traslada exactamente esa validación a `GuardarTransaccionRequest`.

## Base separada

La aplicación usa la base MySQL `taskboard_semana10`, sin compartir tablas con Semanas 5–9. `.env.example` no contiene credenciales; configura las locales en `.env`, que no se sube a Git.

## Ejecutar

Desde `Semana10/taskboard/`:

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan test
php artisan serve --port=8005
```

## Flujo de validación

El formulario muestra errores globales y por campo, conserva valores con `old()`, y `GuardarTransaccionRequest` valida comercio existente, nombre requerido/string/max 255 y monto numérico mayor a cero. Los mensajes están en español; `attributes()` usa nombres amigables.

## Rutas de comprobación

- `http://127.0.0.1:8005/comercios/1/transacciones/nueva`
- POST de registro: ruta nombrada `transacciones.store`.
- Envíos inválidos regresan al formulario con los errores correspondientes.
- El valor inyectado `estado=Completada` no se acepta; las transacciones nuevas permanecen `Iniciada`.

El informe está en [ENTREGA_SEMANA10.md](ENTREGA_SEMANA10.md).
