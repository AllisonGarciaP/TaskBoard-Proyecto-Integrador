# Semana 7 — TaskBoard con Blade

Este avance está separado en `Semana7/taskboard/` y parte del paquete fuente adjunto `taskboard-source`. Incluye listado/detalle de comercios, layout Blade, badges reutilizables y el caso de comercio sin transacciones.

## Requisitos

- PHP 8.2 o superior con `pdo_mysql`.
- Composer.
- MySQL activo.

## Base aislada

La base de esta semana se llama `taskboard_semana7`; no usa las bases de semanas anteriores.

```sql
CREATE DATABASE taskboard_semana7 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

La plantilla `.env.example` usa esa base. En `.env`, configura tu usuario y contraseña local de MySQL.

## Ejecutar

Desde `Semana7/taskboard/`:

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan test
php artisan serve --port=8002
```

Si el servidor de Semana 6 sigue activo, déjalo en el puerto 8001; Semana 7 se sirve en el 8002.

## Páginas

- `/` redirige a `/comercios`.
- `/comercios` lista los comercios y badges de actividad.
- `/comercios/1` muestra transacciones y badges de estado.
- `/comercios/2` muestra el caso `Sin transacciones`.
- `/comercios/999999` devuelve 404 por Route Model Binding.

Para conservar el material recibido, el README fuente completo está en `Semana7/README_FUENTE.md`.