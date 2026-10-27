<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ReservaApiController;
use App\Http\Controllers\Api\TelescopioApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthApiController::class, 'login'])
    ->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/yo', [AuthApiController::class, 'yo']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/tokens', [AuthApiController::class, 'tokens']);
    Route::delete('/tokens/{tokenId}', [AuthApiController::class, 'destroyToken']);

    Route::get('/telescopios', [TelescopioApiController::class, 'index']);
    Route::get('/telescopios/{telescopio}', [TelescopioApiController::class, 'show']);
    Route::post('/telescopios', [TelescopioApiController::class, 'store']);
    Route::delete('/telescopios/{telescopio}', [TelescopioApiController::class, 'destroy']);

    Route::get('/reservas', [ReservaApiController::class, 'index']);
    Route::post('/reservas', [ReservaApiController::class, 'store']);
    Route::get('/reservas/{reserva}', [ReservaApiController::class, 'show']);
    Route::delete('/reservas/{reserva}', [ReservaApiController::class, 'destroy']);
});
