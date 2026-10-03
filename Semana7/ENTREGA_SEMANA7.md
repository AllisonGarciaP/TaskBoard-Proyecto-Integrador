# Universidad Pedagógica de El Salvador

## Facultad de Ingeniería

### Integración de Sistemas · Ciclo 02-2026

**GUÍA DE TRABAJO — SEMANA 7**

**Vistas Blade, layouts y componentes reutilizables**

**Proyecto integrador: TaskBoard — Pasarela de Pagos**

**Docente:** Ing. Oscar Contreras

**Estudiante:** Allison Gabriela Garcia Ponce

**Carné:** GP-64074-23

**Fecha:** 3 de octubre de 2026

---

## Organización por semana

| Carpeta | Avance |
| --- | --- |
| `Semana5/` | Routing y controladores iniciales. |
| `Semana6/` | Migraciones, Eloquent, llaves foráneas y relaciones. |
| `Semana7/` | Este informe, el proyecto Laravel funcional y las vistas Blade. |

Semana 7 usa `Semana7/taskboard/` y la base aislada `taskboard_semana7`. No modifica las bases de semanas previas. El material original recibido está conservado en `README_FUENTE.md`.

## Qué se implementó

- Layout común `layouts.app` con navegación, estilos y footer.
- Listado `comercios.index` con `@forelse`, conteo de transacciones y componente `<x-badge-actividad>`.
- Detalle `comercios.show` con transacciones, `<x-badge-estado>` y estado vacío.
- Route Model Binding para resolver comercios o devolver 404.
- Migraciones de comercios, transacciones y eventos de transacción.
- Seeder repetible con tres comercios: Café Amanecer (2 transacciones), Ferretería El Tornillo (0) y Pupusería Doña Marta (1).

## Instalación y ejecución

Requisitos: PHP 8.2+, Composer, MySQL activo y extensión `pdo_mysql`.

Crear una vez la base MySQL:

```sql
CREATE DATABASE taskboard_semana7 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Desde `Semana7/taskboard/`, configurar `.env` con MySQL local y ejecutar:

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan test
php artisan serve --port=8002
```

El `.env` contiene credenciales locales y no se entrega. `migrate:fresh` no es necesario; usarlo solo en una base desechable.

## Páginas y evidencia HTTP

Servidor local: `http://127.0.0.1:8002`

| URL | Resultado verificado |
| --- | --- |
| `/` | Redirección al listado, HTTP 200 final. |
| `/comercios` | HTML del listado, los tres comercios y badges de actividad, HTTP 200. |
| `/comercios/1` | Café Amanecer, sus 2 transacciones y estados Completada/Iniciada, HTTP 200. |
| `/comercios/2` | Ferretería El Tornillo con `Sin transacciones`, HTTP 200. |
| `/comercios/999999` | HTTP 404 por Route Model Binding. |

La base MySQL quedó con 3 comercios, 3 transacciones y conteos por comercio `2, 0, 1`. Todas las migraciones figuran como `Ran`.

## Pruebas automatizadas

```text
php artisan test tests/Feature/Semana7BladeTest.php
4 pruebas aprobadas, 17 aserciones

php artisan test
6 pruebas aprobadas, 20 aserciones
```

Las pruebas cubren redirección, render del listado, ambos componentes Blade, detalle con y sin transacciones y el 404 para un comercio inexistente.

Para evidencia visual adicional, tomar capturas reales del listado, los detalles `/comercios/1` y `/comercios/2`, y el resultado 404. No se incluyen capturas inventadas.
