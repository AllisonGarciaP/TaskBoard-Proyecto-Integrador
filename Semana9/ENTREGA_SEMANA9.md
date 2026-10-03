# Universidad Pedagógica de El Salvador

## Facultad de Ingeniería

### Integración de Sistemas · Ciclo 02-2026

**GUÍAS PRÁCTICAS N.º 5 Y 6 — SEMANA 9**

**Formularios, búsqueda GET, CSRF y nueva transacción**

**Proyecto integrador: TaskBoard — Pasarela de Pagos**

**Docente:** Ing. Oscar Contreras

**Estudiante:** Allison Gabriela Garcia Ponce

**Carné:** GP-64074-23

**Fecha:** 3 de octubre de 2026

---

## Organización

`Semana9/taskboard/` contiene el proyecto Laravel de los laboratorios de jueves y viernes. Usa `taskboard_semana9`, una base aparte de las semanas 5–8. Las credenciales están solo en el `.env` local y no se incluyen en Git.

## Laboratorio del jueves

- `GET /practica/formulario-demo` presenta controles text, email, number, select y checkbox.
- `POST /practica/enviar` es una ruta sandbox sin escritura a base de datos; el formulario final envía `@csrf` y muestra “Formulario recibido correctamente.”
- `GET /comercios` filtra por nombre mediante `buscar` y por rubro mediante `rubro`. Se pueden combinar y los valores permanecen en los campos.
- La vista de búsqueda usa GET; el formulario POST de prueba no altera los datos de comercios.

Para observar el 419 deliberadamente, quitar temporalmente `@csrf` del sandbox, enviar y verificar el error; luego restaurarlo y recargar la vista antes de enviar otra vez. El estado guardado en el repositorio conserva `@csrf`.

## Laboratorio del viernes

- `GET /comercios/{comercio}/transacciones/nueva` presenta el formulario asociado al comercio.
- `POST /transacciones` recibe el token CSRF y solo acepta `comercio_id`, `cliente_nombre` y `monto` mediante `Request::only()`.
- El estado se establece en `Iniciada` por el valor predeterminado de la migración; un `estado=Completada` añadido manualmente al request se ignora.
- Después de guardar, PRG redirige al panel del comercio y muestra `Transacción registrada con éxito.` como mensaje flash de un solo uso.
- El panel incluye el enlace `+ Nueva transacción`.

## Verificación automatizada

```text
php artisan test
10 pruebas aprobadas, 64 aserciones
```

Las pruebas verifican el sandbox/CSRF token, filtros por nombre y rubro en conjunto, persistencia de transacciones, estado por defecto, protección contra el campo `estado` manipulado, redirección PRG, mensaje flash y que refrescar no duplique la fila.

Base MySQL `taskboard_semana9`: migraciones aplicadas y datos base sembrados (3 comercios; conteos iniciales 2, 0, 1). Para el formulario en navegador iniciar con `php artisan serve --port=8004`.

Verificación HTTP en el servidor local: POST al sandbox sin token CSRF devolvió 419; el mismo POST con token devolvió 200. El alta real redirigió al panel con mensaje flash, ignoró el `estado=Completada` manipulado y persistió el estado `Iniciada`. Se eliminó el registro de prueba después de verificarlo; la base conserva solo los 3 registros de transacción del seeder.

## Evidencias visuales sugeridas

- Sandbox `/practica/formulario-demo` con sus campos.
- Buscador vacío, búsqueda `Amanecer`, búsqueda por rubro y ambos filtros combinados.
- Formulario `/comercios/1/transacciones/nueva`.
- Panel tras guardar: mensaje flash y transacción `Iniciada`.
- Experimento CSRF: 419 sin `@csrf` y éxito al restaurarlo.

Las pruebas HTTP de navegador y el experimento CSRF se deben capturar en el equipo; este informe no presenta capturas inventadas.
