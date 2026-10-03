<?php

namespace App\Http\Controllers;

use App\Models\EventoTransaccion;
use Illuminate\Database\Eloquent\Collection;

class EventoTransaccionController extends Controller
{
    public function index(): Collection
    {
        return EventoTransaccion::with('transaccion.comercio')->orderBy('id')->get();
    }
}