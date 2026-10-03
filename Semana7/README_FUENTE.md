# TaskBoard — Pasarela de Pagos
### Integración de Sistemas · UPED · Ciclo 02-2026
**Docente:** Ing. Oscar Armando Contreras · **Avance:** Semanas 5 a 7 — Unidad II completa

---

## 1. ¿Qué es esto?

Este paquete contiene **todo el código fuente** del proyecto TaskBoard construido en clase entre la Semana 5 y la Semana 7:

| Semana | Tema | Qué aporta a este paquete |
|---|---|---|
| 5 | Routing y Controladores | `routes/web.php`, `ComercioController.php` |
| 6 | Eloquent ORM | Migraciones, modelos `Comercio`, `Transaccion`, `EventoTransaccion` |
| 7 | Blade (layouts y componentes) | `layouts/app.blade.php`, `<x-badge-estado>`, `<x-badge-actividad>`, vistas de comercios |

**No incluye** el "core" de Laravel (carpeta `vendor/`, `bootstrap/`, `public/index.php`, etc.). Esto es intencional: instalarás primero un proyecto Laravel limpio y luego copiarás estos archivos encima, tal como se ha hecho semana a semana en clase.

> 📌 **Importante — Semana 8 (jueves):** este proyecto es deliberadamente el punto de partida **sin** los ajustes de la sesión del jueves de la Semana 8. Ese día se trabaja en vivo, en pareja/equipo, usando la **Guía de Trabajo N.º 3** junto con este mismo código como base. Las actividades de esa guía (Detective de Bugs, Ruleta Blade, predicción de código) están pensadas para practicarse directamente sobre este proyecto, no sobre una versión ya resuelta.

---

## 2. Requisitos previos

Instala esto **antes** de empezar (una sola vez en tu computadora):

| Herramienta | Versión mínima | Cómo verificar |
|---|---|---|
| PHP | 8.2 | `php -v` |
| Composer | 2.x | `composer -V` |
| MySQL (o MariaDB) | 8.x / 10.x | `mysql --version` |
| Extensión PHP `pdo_mysql` | — | `php -m \| grep pdo_mysql` |

> 💡 Si usas Laravel Herd, XAMPP, Laragon o WAMP, PHP, Composer y MySQL ya suelen venir incluidos.

---

## 3. Paso a paso para levantar el proyecto

### Paso 1 — Crear un proyecto Laravel limpio

Abre una terminal en la carpeta donde quieras guardar tus proyectos y ejecuta:

```bash
composer create-project laravel/laravel taskboard
cd taskboard
```

Esto descarga un Laravel nuevo y funcional dentro de la carpeta `taskboard/`.

> **Nota sobre `bootstrap/app.php`:** no necesitas tocar nada dentro de la carpeta `bootstrap/`. En Laravel 11+, ese archivo ya viene configurado de fábrica para leer las rutas desde `routes/web.php`:
> ```php
> ->withRouting(
>     web: __DIR__.'/../routes/web.php',
>     commands: __DIR__.'/../routes/console.php',
>     health: '/up',
> )
> ```
> Como en el Paso 2 solo **reemplazas el contenido** de `routes/web.php` (sin cambiar su nombre ni ubicación), `bootstrap/app.php` sigue apuntando correctamente sin que tengas que editarlo.

### Paso 2 — Copiar los archivos de este paquete

Copia el contenido de `taskboard-source/` **encima** de tu proyecto `taskboard/`, respetando las rutas (sobrescribe `routes/web.php` y `database/seeders/DatabaseSeeder.php` cuando te lo pida).

```bash
# Ejemplo en Linux/Mac, ejecutado UN NIVEL ARRIBA de ambas carpetas:
cp -r taskboard-source/app/Models/*        taskboard/app/Models/
cp -r taskboard-source/app/Http/Controllers/* taskboard/app/Http/Controllers/
cp -r taskboard-source/database/migrations/*  taskboard/database/migrations/
cp -r taskboard-source/database/seeders/*     taskboard/database/seeders/
cp    taskboard-source/routes/web.php         taskboard/routes/web.php
cp -r taskboard-source/resources/views/*      taskboard/resources/views/
```

En Windows (PowerShell), usa `Copy-Item -Recurse` en lugar de `cp -r`. Si prefieres hacerlo a mano: crea cada carpeta/archivo indicado y pega el contenido correspondiente (son solo 13 archivos, ver el árbol en la sección 6).

### Paso 3 — Configurar la base de datos

1. Crea la base de datos en MySQL:
   ```sql
   CREATE DATABASE taskboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Abre el archivo `.env` (en la raíz de `taskboard/`) y ajusta estas líneas:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=taskboard
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   (usa tu propio usuario/contraseña de MySQL si no es `root` sin contraseña).

### Paso 4 — Migrar y sembrar datos de ejemplo

Desde la carpeta `taskboard/`:

```bash
php artisan migrate
php artisan db:seed
```

Esto crea las tablas `comercios`, `transacciones` y `eventos_transaccion`, y las llena con los datos de ejemplo usados en todas las guías de clase:

- **Café Amanecer** (Restaurante) — 2 transacciones: `$45.00 María López — Completada` y `$12.50 Juan Pérez — Iniciada`.
- **Ferretería El Tornillo** (Ferretería) — 0 transacciones (para probar el caso `@empty`).
- **Pupusería Doña Marta** (Restaurante) — 1 transacción `Fallida`, como variedad extra.

> Si prefieres hacerlo en un solo paso: `php artisan migrate:fresh --seed`.

### Paso 5 — Levantar el servidor

```bash
php artisan serve
```

Verás algo como `Server running on [http://127.0.0.1:8000]`.

### Paso 6 — Probar en el navegador

| URL | Qué deberías ver |
|---|---|
| `http://127.0.0.1:8000/` | Redirige automáticamente a `/comercios`. |
| `http://127.0.0.1:8000/comercios` | Listado de los 3 comercios, cada uno con su badge Activo/Sin actividad. |
| `http://127.0.0.1:8000/comercios/1` | Panel de Café Amanecer con sus 2 transacciones y sus badges de estado. |
| `http://127.0.0.1:8000/comercios/2` | Panel de Ferretería El Tornillo con el mensaje "Sin transacciones" (caso `@empty`). |

---

## 4. Checklist de verificación

Marca cada punto antes de dar por buena la instalación:

- [ ] `php artisan serve` inicia sin errores.
- [ ] `/comercios` muestra 3 comercios con el conteo correcto de transacciones.
- [ ] Café Amanecer muestra la etiqueta **Activo** (verde); Ferretería El Tornillo muestra **Sin actividad** (gris).
- [ ] `/comercios/1` muestra las 2 transacciones con sus badges de estado (✔ Completada, ⏳ Iniciada).
- [ ] `/comercios/2` (Ferretería) muestra "Sin transacciones" en vez de una lista vacía o un error.
- [ ] El layout (`<nav>` y `<footer>`) se ve igual en ambas páginas — prueba de que `@extends` funciona.

---

## 5. Solución de problemas comunes

| Situación | Causa probable | Solución |
|---|---|---|
| `SQLSTATE[HY000] [1049] Unknown database 'taskboard'` | No creaste la base de datos. | Ejecuta el `CREATE DATABASE` del Paso 3. |
| `Undefined variable $comercio` | El controlador no envió esa variable a la vista. | Verifica `compact('comercio')` en `ComercioController@show`. |
| `View [comercios.show] not found` | Archivo o carpeta mal nombrado. | El punto representa una barra: `comercios.show` = `comercios/show.blade.php`. |
| `Component "badge-estado" not found` | El nombre del archivo no coincide con la etiqueta. | Debe llamarse en kebab-case: `badge-estado.blade.php` dentro de `resources/views/components/`. |
| Página en blanco o error 404 en `/comercios/1` | El servidor no está corriendo o la migración no se ejecutó. | Repite `php artisan serve` y `php artisan migrate:fresh --seed`. |
| `Class "App\Models\Comercio" not found` | Composer no ha regenerado el autoload. | Ejecuta `composer dump-autoload`. |

---

## 6. Árbol de archivos de este paquete

```
taskboard-source/
├── app/
│   ├── Http/Controllers/
│   │   └── ComercioController.php        (Semana 5)
│   └── Models/
│       ├── Comercio.php                  (Semana 6)
│       ├── Transaccion.php               (Semana 6)
│       └── EventoTransaccion.php         (Semana 6)
├── database/
│   ├── migrations/
│   │   ├── 2024_06_01_000001_create_comercios_table.php
│   │   ├── 2024_06_01_000002_create_transacciones_table.php
│   │   └── 2024_06_01_000003_create_eventos_transaccion_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── ComercioSeeder.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php                 (Semana 7)
│   ├── components/
│   │   ├── badge-estado.blade.php        (Semana 7)
│   │   └── badge-actividad.blade.php     (Semana 7)
│   └── comercios/
│       ├── index.blade.php               (Semana 7)
│       └── show.blade.php                (Semana 7)
├── routes/
│   └── web.php                           (Semana 5)
└── README.md                             (este archivo)
```

---

## 7. Modelo de datos (resumen)

```
Comercio  1 ──< N  Transaccion  1 ──< N  EventoTransaccion
```

- **Comercio**: `nombre_comercio`, `rubro`, `telefono`.
- **Transaccion**: `comercio_id`, `monto`, `cliente_nombre`, `estado` (`Iniciada` | `Completada` | `Fallida`).
- **EventoTransaccion**: `transaccion_id`, `tipo_evento`, `detalle` — bitácora de auditoría, lista para usarse en semanas futuras.

---

## 8. Cómo se usa este proyecto en la Semana 8 (jueves)

Este código es el **punto de partida** de la sesión de repaso integrador. Ese día, junto con la **Guía de Trabajo N.º 3 — Semana 8**, los estudiantes:

1. Levantan este mismo proyecto (Pasos 1 a 6 de esta guía).
2. Usan el diagrama de flujo y las autoevaluaciones de la guía para explicar, sobre este código real, cómo viaja una petición (`Route → Controlador → Eloquent → Blade`).
3. Resuelven en vivo la dinámica **"Detective de Bugs"**: el docente proyecta una variante con un error intencional (no incluida en este paquete) y la clase la diagnostica.
4. Predicen el output de un fragmento de `comercios/index.blade.php` con `<x-badge-actividad>` **antes** de verlo renderizado en este proyecto.
5. Participan en la **Ruleta Blade** apoyándose en el código real como referencia.

De esta forma, el jueves se vive como **práctica genuina de integración**, no como la simple entrega de un ejercicio ya resuelto.

---

## 9. Próximos pasos (Semana 9 en adelante)

Este proyecto queda listo como base para continuar con:

- Formularios (`GET`/`POST`) y protección CSRF para registrar nuevas transacciones.
- Validación de datos de entrada (`$request->validate()`).
- CRUD completo de comercios y transacciones.

---

**Ing. Oscar Armando Contreras** · oscar.contreras@uped.edu.sv
Integración de Sistemas · UPED · Ciclo 02-2026
