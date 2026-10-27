<?php

use App\Http\Controllers\Api\ReservaApiController;
use App\Http\Controllers\Api\TelescopioApiController;
use Illuminate\Support\Facades\Route;

Route::get('/telescopios', [TelescopioApiController::class, 'index']);
Route::post('/telescopios', [TelescopioApiController::class, 'store']);
Route::get('/telescopios/{telescopio}', [TelescopioApiController::class, 'show']);
Route::delete('/telescopios/{telescopio}', [TelescopioApiController::class, 'destroy']);

Route::get('/reservas', [ReservaApiController::class, 'index']);
Route::post('/reservas', [ReservaApiController::class, 'store']);
Route::get('/reservas/{reserva}', [ReservaApiController::class, 'show']);
Route::delete('/reservas/{reserva}', [ReservaApiController::class, 'destroy']);
