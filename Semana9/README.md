# Semana 9 — Formularios y transacciones

Proyecto Laravel separado en `Semana9/taskboard/`, basado en el estado de Semana 8 y conectado a la base MySQL aislada `taskboard_semana9`.

## Configuración

Requisitos: PHP 8.2+, Composer, MySQL activo y `pdo_mysql` habilitado. Crear una vez la base:

```sql
CREATE DATABASE taskboard_semana9 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configura tus credenciales en `Semana9/taskboard/.env`. No compartas ese archivo.

Desde `Semana9/taskboard/`:

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan test
php artisan serve --port=8004
```

## Laboratorios incluidos

- Sandbox de formulario: `GET /practica/formulario-demo` con text, email, number, select y checkbox.
- Experimento POST seguro: `/practica/enviar`, protegido con `@csrf` en la vista final.
- Buscador GET de comercios por nombre y rubro; permite combinar filtros y mantiene valores seleccionados.
- Registro real de transacciones desde el detalle del comercio, usando `@csrf`, `Request::only()` y estado predeterminado `Iniciada`.
- Patrón PRG: después de guardar redirige al comercio y muestra mensaje flash de un solo uso.

El formulario sandbox puede cambiarse temporalmente a GET o quitarse `@csrf` para completar las observaciones de clase; restaurar POST y `@csrf` al terminar.

## URLs

- `http://127.0.0.1:8004/practica/formulario-demo`
- `http://127.0.0.1:8004/comercios`
- `http://127.0.0.1:8004/comercios?buscar=Amanecer`
- `http://127.0.0.1:8004/comercios?buscar=Amanecer&rubro=Restaurante`
- `http://127.0.0.1:8004/comercios/1/transacciones/nueva`

Informe de entrega y evidencia: [ENTREGA_SEMANA9.md](ENTREGA_SEMANA9.md).
