# Universidad Pedagógica de El Salvador

## Facultad de Ingeniería

### Integración de Sistemas · Ciclo 02-2026

**GUÍAS PRÁCTICAS N.º 7 Y 8 — SEMANA 10**

**Validación de formularios y Form Requests**

**Proyecto integrador: TaskBoard — Pasarela de Pagos**

**Docente:** Ing. Oscar Contreras

**Estudiante:** Allison Gabriela Garcia Ponce

**Carné:** GP-64074-23

**Fecha:** 3 de octubre de 2026

---

## Separación de avances

`Semana10/taskboard/` continúa el proyecto de Semana 9 en una carpeta y base de datos independientes (`taskboard_semana10`). Las bases anteriores no se modifican. El `.env` contiene configuración local y no debe publicarse.

## Guía N.º 7 — Validación inline

`TransaccionController@store` valida antes de escribir:

- `comercio_id`: requerido y debe existir en `comercios.id`.
- `cliente_nombre`: requerido, texto y máximo 255 caracteres.
- `monto`: requerido, numérico y mínimo `0.01`.
- Los mensajes de cliente y monto son personalizados en español.

La vista conserva lo escrito con `old()`, muestra errores junto a cada campo y contiene un resumen accesible de errores. No se añadió un `FormRequest` todavía en este paso.

## Guía N.º 8 — Form Request

La misma validación fue migrada a `App\Http\Requests\GuardarTransaccionRequest`, generado siguiendo el comando Artisan. Incluye `authorize(): true`, `rules()`, `messages()` y `attributes()`. El controlador recibe `GuardarTransaccionRequest $request` y usa `only()` al crear la transacción. El comportamiento externo se mantiene, mientras la clase organiza las reglas y permite reutilizarlas.

No se conservó una ruta temporal de depuración del reto: el `FormRequest` está conectado al flujo real de registro.

## Verificación

- La base `taskboard_semana10` aplicó las migraciones y recibió los datos semilla.
- Las pruebas validan reglas, mensajes, retención de campos, existencia del comercio, estado predeterminado `Iniciada` y que el estado manipulado no se persista.
- Las pruebas también verifican que el guardado exitoso conserva PRG, redirige al comercio y muestra el mensaje flash.
- La suite completa pasó: 14 pruebas y 89 aserciones.
- En HTTP se verificaron errores visibles para monto inválido y comercio inexistente, retención con `old()`, y el envío válido con flash. Un `estado=Completada` manipulado se ignoró; la fila de prueba se eliminó al terminar.
- MySQL conserva únicamente los 3 registros iniciales del seeder después de las pruebas manuales.

Ejecutar desde `Semana10/taskboard/`:

```powershell
php artisan migrate:status
php artisan test
php artisan serve --port=8005
```

Formulario para prueba: `http://127.0.0.1:8005/comercios/1/transacciones/nueva`.

La suite completa ejecuta las regresiones anteriores de Semanas 7–9 además de las pruebas de Semana 10. Antes de entregar, probar desde el navegador campos vacíos, texto en monto, monto cero/negativo, comercio inexistente y un envío válido; capturar evidencia real si la plataforma la requiere.
