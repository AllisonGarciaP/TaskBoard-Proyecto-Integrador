# ENTREGA — SEMANA 13
## Integración de Sistemas
### Servicios Web REST con Laravel, JSON y API Resources

**Estudiante:** Allison Gabriela Garcia Ponce
**Universidad:** Universidad Pedagógica de El Salvador  
**Asignatura:** Integración de Sistemas  
**Semana:** 13

### 1. Repositorio de GitHub

https://github.com/AllisonGarciaP/TaskBoard-Proyecto-Integrador

### 2. Objetivo

Implementar una API REST en Laravel para consultar, crear y eliminar telescopios y reservas, utilizando respuestas JSON, API Resources y validaciones.

### 3. Archivos principales

- `taskboard/routes/api.php`
- `taskboard/app/Http/Controllers/Api/TelescopioApiController.php`
- `taskboard/app/Http/Controllers/Api/ReservaApiController.php`
- `taskboard/app/Http/Resources/TelescopioResource.php`
- `taskboard/app/Http/Resources/ReservaResource.php`
- `taskboard/pruebas-api.http`

### 4.Pruebas

Se realizaron pruebas de los siguientes endpoints:

- Listado y consulta individual de telescopios.
- Creación de telescopios.
- Filtro de telescopios activos.
- Listado y filtro de reservas por estado.
- Consulta y creación de reservas.
- Respuestas de error 404 y 422.
- Eliminación de reservas y telescopios con respuesta 204.


### 5. Conclusión

La práctica permitió implementar y comprobar operaciones de una API REST con Laravel, utilizando JSON, API Resources, filtros y validaciones para gestionar telescopios y reservas.