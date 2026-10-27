<?php

namespace App\Http\Controllers;

use App\Models\Telescopio;

class TelescopioController extends Controller
{
    public function index()
    {
        // Cada telescopio llega con su número de reservas en $t->reservas_count (necesita la relación de la Tarea 1).
        $telescopios = Telescopio::withCount('reservas')->orderBy('id')->get();

        return view('telescopios.index', compact('telescopios'));
    }

    public function show(Telescopio $telescopio)
    {
        $telescopio->load('reservas.user');

        return view('telescopios.show', compact('telescopio'));
    }
}
