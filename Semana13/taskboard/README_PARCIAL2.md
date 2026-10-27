# Parcial II · Integración de Sistemas (CE-ISC019) · Observatorio

Mini proyecto Laravel 12 (SQLite). Debes **completar los `TODO (Tarea N)`** del código y luego **medir tu propio código** con comandos que te dan UN número por pregunta. Ese número lo escribes en Moodle. Cada estudiante recibe una **semilla distinta** en cada pregunta (Moodle te la muestra): los datos y los casos de prueba cambian con ella, así que la respuesta de un compañero no te sirve.

## Preparación (una sola vez)
1. Descomprime el proyecto y abre una terminal en esa carpeta.
2. `php artisan observatorio:diagnostico` → debe terminar con **LISTO**.
3. No necesitas `composer install` (la carpeta `vendor` ya viene incluida) ni modificar `.env`.

## Cómo responder cada pregunta práctica
```
php artisan observatorio:probar pNN SEMILLA
```
Ejemplo: `php artisan observatorio:probar p03 57`. (`pNN` y `SEMILLA` los indica Moodle en cada pregunta.)
Al final imprime `RESULTADO: N` → escribe **N** en Moodle.
Si dice "La sonda no pudo completarse", tu código de esa tarea tiene un error: léelo, corrígelo y repite. `php artisan observatorio:probar --lista` muestra todas las sondas.
`php artisan observatorio:semilla SEMILLA` reinicia la base con los datos de esa semilla (para que explores con `tinker` o el navegador con `php artisan serve`).

## Tareas
**Tarea 1 · Modelos y relaciones (Eloquent)** — `Telescopio` tiene muchas `reservas()`; `User` tiene muchas `reservas()`; `Reserva` pertenece a `telescopio()` y a `user()`. Declara `$fillable` en `Reserva` (`user_id`, `telescopio_id`, `fecha`, `horas`, `objetivo`, `estado`).

**Tarea 2 · Vistas Blade** — `telescopios/index`: recorre `$telescopios` y muestra **solo los activos** como `<tr data-telescopio="ID">`; acumula en `$total` la suma de `reservas_count` de los mostrados. `telescopios/show`: lista las reservas con `@forelse` como `<li data-reserva="ID">`; si no hay, un único `<li>` con un aviso **sin** `data-reserva`. No cambies los atributos `data-*` ni `id="total-reservas"`.

**Tarea 3 · Formulario y validación** — `@csrf` en `reservas/create`; preselecciona con `@selected` el telescopio de `?telescopio=ID` (o `old()`); en `ReservaController@store` crea la reserva del usuario autenticado con `estado` pendiente y redirige a `reservas.index`. En `StoreReservaRequest`:

| Campo | Reglas |
|---|---|
| telescopio_id | requerido, entero, existe en `telescopios` |
| fecha | requerida, fecha, igual o posterior a 2026-11-01 |
| horas | requerida, entero, entre 1 y 4 |
| objetivo | requerido, texto, mínimo 3, máximo 60 |

Mensajes personalizados (texto **exacto**):
- `objetivo.required` → `Debes indicar el objetivo de la observación.`
- `horas.between` → `Las horas deben estar entre :min y :max.`
- `fecha.after_or_equal` → `La fecha debe ser el 1 de noviembre de 2026 o posterior.`
- `telescopio_id.exists` → `El telescopio elegido no existe.`

**Tarea 4 · CRUD** — `update` (actualizar), `destroy` (eliminar) y `cambiarEstado` (validar que `estado` sea `pendiente`, `confirmada` o `cancelada`, guardar y volver al listado).

**Tarea 5 · Autenticación y autorización** — `AuthController`: `login` (valida, `Auth::attempt`, regenera sesión, redirige a `reservas.index`; si falla, vuelve atrás con error), `registrar` (valida `name`, `email` único, `password` mínimo 8 y confirmado; crea el usuario, inicia sesión y redirige a `reservas.index`), `logout` (cierra, invalida sesión, regenera token, redirige a `login`). `ReservaController@index` muestra **solo** las reservas del usuario autenticado; `autorizar()` responde **403** si la reserva no es suyo.

## Reglas
- Edita solo donde dice `TODO`. **No modifiques** `app/Observatorio/`, `app/Console/`, `routes/` ni `database/` (es la infraestructura de medición).
- Usuarios de práctica: `ana@obs.test`, `luis@obs.test`, `marta@obs.test`, clave `clave1234`.
- Puedes usar IA y tus apuntes. Hay también preguntas conceptuales en Moodle (predecir/diagnosticar código): lee con calma.
- Trabaja con tiempo: cada sonda tarda unos segundos; el cronómetro de Moodle es de 60 minutos.
