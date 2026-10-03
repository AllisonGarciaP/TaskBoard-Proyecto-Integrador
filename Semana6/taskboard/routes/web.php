<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercioController;
use App\Http\Controllers\EventoTransaccionController;
use App\Http\Controllers\TransaccionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/taskboard', function () {
    return 'Bienvenido a TaskBoard, tu pasarela de pagos.';
});

Route::get('/acerca-de', function () {
    return 'TaskBoard registra comercios afiliados, pagos y el historial de cada cambio de estado.';
});

Route::get('/proyecto', function () {
    return ['nombre' => 'TaskBoard', 'ciclo' => '02-2026'];
});

Route::get('/contacto', function () {
    return 'Allison Gabriela Garcia Ponce | Correo institucional: ' . config('taskboard.student_email');
});

Route::get('/comercios/nombres', function () {
    return ['Café Amanecer', 'Ferretería San José', 'Pupusería El Buen Sabor'];
});

Route::get('/comercios/por-categoria/{categoria?}', function ($categoria = 'todos') {
    return "Mostrando comercios de la categoría: $categoria";
})->name('comercios.categoria');

Route::get('/estados', function () {
    return ['Iniciada', 'Procesando', 'Aprobada', 'Rechazada', 'Liquidada'];
});

Route::get('/transaccion/demo', function () {
    return [
        'id' => 1,
        'comercio' => 'Café Amanecer',
        'monto' => 25.50,
        'moneda' => 'USD',
        'estado' => 'Aprobada',
    ];
});

Route::prefix('comercios')->name('comercios.')->group(function () {
    Route::get('/', [ComercioController::class, 'index'])->name('index');
    Route::get('/{comercio}', [ComercioController::class, 'show'])
        ->whereNumber('comercio')
        ->name('show');
});

Route::get('/comercio/PupuseriaElSalvador', function () {
    return '¡Bienvenido a TaskBoard, PupuseriaElSalvador!';
});

Route::get('/comercio/{comercio}', [ComercioController::class, 'show'])
    ->whereNumber('comercio')
    ->name('comercio.show');

Route::get('/transacciones', [TransaccionController::class, 'index'])->name('transacciones.index');
Route::get('/transaccion/{transaccion}', [TransaccionController::class, 'show'])
    ->whereNumber('transaccion')
    ->name('transacciones.show');

Route::get('/eventos-transaccion', [EventoTransaccionController::class, 'index'])
    ->name('eventos-transaccion.index');
