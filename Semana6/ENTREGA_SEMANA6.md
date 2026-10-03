# Universidad Pedagógica de El Salvador

## Facultad de Ingeniería

### Integración de Sistemas · Ciclo 02-2026

**GUÍAS DE TRABAJO DE SEMANA 6**

**Eloquent ORM, migraciones, relaciones y datos reales**

**Proyecto integrador: TaskBoard — Pasarela de Pagos**

**Docente:** Ing. Oscar Contreras

**Estudiante:** Allison Gabriela Garcia Ponce

**Carné:** GP-64074-23

**Correo institucional:** configurar antes de entregar

**Fecha:** 2 de octubre de 2026

---

## Separación de avances

| Carpeta | Avance |
| --- | --- |
| `Semana5/` | Rutas y controladores introductorios de las guías anteriores. |
| `Semana6/` | Este informe y el proyecto Laravel con migraciones y relaciones Eloquent. |
| Raíz del repositorio | Proyecto PHP/PDO anterior; sus tablas Kanban no se usan ni se modifican. |

El proyecto de esta entrega está en `Semana6/taskboard/`. Se usa una base aparte, `taskboard_semana6`, porque la base preexistente `taskboard` contiene las tablas del Kanban (`tableros`, `columnas`, `tareas`).

## Configuración y comandos

PHP debe tener activadas `pdo_mysql` y `pdo_sqlite`. En MySQL, crear la base vacía una vez:

```sql
CREATE DATABASE taskboard_semana6 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configurar los datos locales en `.env` (`DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=taskboard_semana6`, usuario y contraseña propios). El `.env` no se comparte ni se entrega. Antes de entregar, sustituir `TASKBOARD_STUDENT_EMAIL` por el correo institucional real.

Desde `Semana6/taskboard/`:

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan migrate:status
php artisan test
php artisan serve --port=8001
```

La base separada contiene 3 comercios, 5 transacciones y 4 eventos de ejemplo. El seeder se puede ejecutar de nuevo sin duplicar esos registros.

## Guía N.º 1: migraciones y modelos

Se implementaron migraciones ordenadas para:

| Tabla | Campos principales y restricciones |
| --- | --- |
| `comercios` | `nombre_comercio`, `rubro`, `fecha_afiliacion`, timestamps; nombre único en una migración posterior. |
| `transacciones` | FK `comercio_id`, monto decimal, moneda, cliente, método de pago, estado y timestamps. |
| `eventos_transaccion` | FK `transaccion_id`, estado anterior/nuevo y timestamps. |

La segunda migración de comercios agrega `telefono` y `correo_contacto` como `nullable`. Los modelos declaran `$fillable`; `Transaccion` y `EventoTransaccion` declaran explícitamente sus nombres de tabla en español para evitar la pluralización inglesa de Laravel.

Los comandos `migrate`, `migrate:status`, `migrate:rollback` y `make:model -m` se usan en el proyecto. La migración de nombre único tiene `down()` para poder revertirse. Los rollbacks borran tablas/datos de desarrollo: hacerlos solo en la base aislada y después de guardar lo necesario.

## Guía N.º 2: relaciones Eloquent y consultas

Se definieron las relaciones en ambas direcciones:

| Modelo | Relación |
| --- | --- |
| `Comercio` | `transacciones(): hasMany(Transaccion::class)` |
| `Transaccion` | `comercio(): belongsTo(Comercio::class)` y `eventos(): hasMany(EventoTransaccion::class)` |
| `EventoTransaccion` | `transaccion(): belongsTo(Transaccion::class)` |

Los controladores ya consultan MySQL, no arreglos: `/comercios` carga `transacciones`, `/transacciones` carga `comercio` y `/eventos-transaccion` carga `transaccion.comercio`. Route Model Binding está conectado en `/comercio/{comercio}` y `/transaccion/{transaccion}`; IDs inexistentes responden 404. También quedó implementada la categoría opcional, el catálogo de estados y la respuesta demostrativa de Semana 5.

## Ejercicios

- Nuevas columnas nullable para teléfono y correo de contacto.
- Tabla y modelo de eventos con su llave foránea.
- Índice `unique` para el nombre del comercio; la prueba confirma que la base rechaza duplicados.
- Seeder repetible para probar Tinker y las relaciones con datos guardados.
- Prueba N+1: con 5 transacciones, consulta las relaciones con 6 consultas sin `with()` y con 2 consultas usando `with('comercio')`.
- Reversibilidad mediante `down()` en todas las migraciones. Probar `rollback` únicamente en una base desechable para no borrar el conjunto de muestra.

## Verificación y evidencia

La base aislada aplicó todas las migraciones en MySQL. Se ejecutó `db:seed` dos veces y se mantuvieron 3 comercios, 5 transacciones y 4 eventos. La prueba automatizada valida columnas, llaves foráneas, relaciones, JSON anidado, binding/404, unicidad, seeder repetible y recuento N+1.

Capturas del navegador/terminal para adjuntar si la plataforma exige imágenes: `php artisan migrate:status`, `php artisan test`, rutas `/comercios`, `/transacciones`, `/transaccion/1`, `/eventos-transaccion` y `/transaccion/999999` (404). No se inventan capturas; deben tomarse al ejecutar la aplicación local.