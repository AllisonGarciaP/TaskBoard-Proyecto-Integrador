<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use Illuminate\Database\Eloquent\Collection;

class TransaccionController extends Controller
{
    public function index(): Collection
    {
        return Transaccion::with('comercio')->orderBy('id')->get();
    }

    public function show(Transaccion $transaccion): Transaccion
    {
        return $transaccion->load('comercio');
    }
}