# Universidad Pedagógica de El Salvador

## Facultad de Ingeniería

### Integración de Sistemas · Ciclo 02-2026

**GUÍAS DE TRABAJO N.º 3 Y 4 — SEMANA 8**

**Repaso integrador y panel de comercio**

**Proyecto integrador: TaskBoard — Pasarela de Pagos**

**Docente:** Ing. Oscar Contreras

**Estudiante:** Allison Gabriela Garcia Ponce

**Carné:** GP-64074-23

**Fecha:** 3 de octubre de 2026

---

## Separación semanal

| Carpeta | Contenido |
| --- | --- |
| `Semana5/` | Rutas y controladores iniciales. |
| `Semana6/` | Migraciones, modelos y relaciones Eloquent. |
| `Semana7/` | Layout y componentes Blade. |
| `Semana8/` | Repaso integrador y resumen de actividad en `show.blade.php`. |

El proyecto se ejecuta desde `Semana8/taskboard/` y usa `taskboard_semana8`, una base independiente para conservar los datos de las semanas anteriores.

## Ejemplo guiado integrado

Al abrir un detalle, el recorrido es:

1. `GET /comercios/{comercio}` coincide con la ruta.
2. `ComercioController@show` recibe el modelo mediante Route Model Binding.
3. El controlador carga `transacciones` antes de enviar los datos a Blade.
4. `comercios/show.blade.php` extiende `layouts.app` y usa `<x-badge-estado>`.
5. Blade renderiza datos escapados y el resumen de actividad.

El controlador usa `load('transacciones')`, y el listado usa `withCount('transacciones')`, evitando consultas N+1.

## Ejercicio: resumen de actividad

El resumen se muestra antes del listado de transacciones:

| Cantidad | Mensaje renderizado | Comercio de prueba |
| --- | --- | --- |
| 0 | Este comercio es nuevo, aún no registra actividad. | Ferretería El Tornillo |
| 1 | Este comercio tiene su primera transacción registrada. | Pupusería Doña Marta |
| 2 o más | Este comercio tiene un historial de N transacciones. | Café Amanecer, N = 2 |

## Repaso y dinámica

- En la ruta, `GET` consulta/representa información; `POST` envía datos para crear o procesar una operación. Por ejemplo, consultar `/comercios` es GET; registrar una transacción desde un formulario sería POST.
- `Route::resource('comercios', ComercioController::class)` genera las rutas CRUD de index, create, store, show, edit, update y destroy.
- Las consultas pertenecen al modelo/controlador y el HTML a Blade: separar las capas permite reutilizar y probar cada parte.
- `withCount('transacciones')` obtiene el total sin cargar cada transacción; `with('transacciones')` carga la relación para recorrerla.
- `@forelse` permite mostrar una alternativa cuando la colección está vacía; `@empty` no se ejecuta si hay elementos.
- `{{ }}` escapa HTML y es la opción segura para datos de usuarios; evitar `{!! !!}` con contenido no confiable.

**Detective de Bugs:** `$comercio->nombreComercio` no coincide con el atributo de base/modelo `nombre_comercio`; debe usarse snake_case. Un comercio sin transacciones no debe romper el listado: debe cubrirse el caso vacío con `@forelse`/`@empty`.

**Predicción Blade:** con Café Amanecer (2 transacciones) y Ferretería El Tornillo (0) se renderizan dos `<li>`; el primero muestra badge Activo y el segundo Sin actividad. `@empty` solo se ejecuta si no hay comercios en la colección.

Las dinámicas presenciales de equipo, la tarjeta Kanban personal y la reflexión individual se realizan en clase y no se representan como participación inventada en este informe.

## Verificación

Base de datos `taskboard_semana8` migrada y sembrada. Conteos comprobados: 3 comercios y transacciones por comercio `2, 0, 1`.

Prueba focalizada del nuevo comportamiento: **1 prueba aprobada, 6 aserciones**. Incluye los tres textos esperados en las rutas `/comercios/1`, `/comercios/2` y `/comercios/3`.

Suite completa del proyecto: **7 pruebas aprobadas, 26 aserciones**. Verificación HTTP adicional: las tres páginas de resumen devolvieron 200 con el mensaje esperado; `/comercios/999999` devolvió 404.

Comandos:

```powershell
php artisan migrate:status
php artisan test
php artisan serve --port=8003
```

Servidor local: `http://127.0.0.1:8003/comercios`.
