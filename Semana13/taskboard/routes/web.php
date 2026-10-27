<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\TelescopioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/telescopios');

Route::get('/telescopios', [TelescopioController::class, 'index'])->name('telescopios.index');
Route::get('/telescopios/{telescopio}', [TelescopioController::class, 'show'])->name('telescopios.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'mostrarRegistro'])->name('register');
    Route::post('/register', [AuthController::class, 'registrar']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::patch('/reservas/{reserva}/estado', [ReservaController::class, 'cambiarEstado'])->name('reservas.estado');
    Route::resource('reservas', ReservaController::class);
});

// Rutas auxiliares que solo existen cuando corren las sondas del parcial (no modificar).
if (env('OBS_SONDA')) {
    require __DIR__.'/sonda.php';
}
