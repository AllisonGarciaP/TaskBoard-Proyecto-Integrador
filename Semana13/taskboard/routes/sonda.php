<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
 * Rutas auxiliares de las sondas del parcial. Solo se cargan si OBS_SONDA está definida
 * (la define el comando observatorio:probar al levantar su servidor temporal).
 * NO MODIFICAR.
 */

Route::get('/_sonda/token', fn () => csrf_token());

Route::get('/_sonda/entrar/{id}', function (int $id) {
    Auth::loginUsingId($id);
    request()->session()->regenerate();

    return csrf_token();
});

Route::get('/_sonda/errores', function () {
    $bolsa = session('errors');

    return response()->json($bolsa ? $bolsa->getBag('default')->toArray() : []);
});
