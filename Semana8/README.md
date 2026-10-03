# Semana 8 — Repaso integrador

Este avance integra Routing, Eloquent y Blade en un proyecto Laravel separado en `Semana8/taskboard/`. El resumen de actividad solicitado el viernes está aplicado en el detalle de comercios.

## Base de datos

Usa MySQL y una base exclusiva, `taskboard_semana8`, distinta a las de Semanas 5, 6 y 7. Crear una vez:

```sql
CREATE DATABASE taskboard_semana8 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configura usuario y contraseña en `Semana8/taskboard/.env`; no compartas ese archivo.

## Iniciar

Desde `Semana8/taskboard/`:

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan test
php artisan serve --port=8003
```

La base de ejemplo incluye Café Amanecer (2 transacciones), Ferretería El Tornillo (0) y Pupusería Doña Marta (1).

## Rutas

- `http://127.0.0.1:8003/comercios`
- `http://127.0.0.1:8003/comercios/1` muestra el resumen para varias transacciones.
- `http://127.0.0.1:8003/comercios/2` muestra el resumen para cero transacciones.
- `http://127.0.0.1:8003/comercios/3` muestra el resumen para una transacción.
- `http://127.0.0.1:8003/comercios/999999` debe devolver 404.

Ver informe de actividades y resultados en [ENTREGA_SEMANA8.md](ENTREGA_SEMANA8.md).
