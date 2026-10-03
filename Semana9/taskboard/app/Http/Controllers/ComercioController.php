<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComercioController extends Controller
{
    /**
     * Semana 5 · Routing y Controladores
     * GET /comercios
     *
     * Lista todos los comercios afiliados junto con el conteo de sus
     * transacciones. withCount() evita el problema N+1: en vez de
     * disparar una consulta extra por cada comercio dentro de la vista,
     * trae el conteo ya resuelto en la consulta principal.
     */
    public function index(Request $request): View
    {
        $comercios = Comercio::query()
            ->when($request->filled('buscar'), fn ($query) => $query->where(
                'nombre_comercio',
                'like',
                '%' . $request->input('buscar') . '%'
            ))
            ->when($request->filled('rubro'), fn ($query) => $query->where(
                'rubro',
                $request->input('rubro')
            ))
            ->withCount('transacciones')
            ->orderBy('nombre_comercio')
            ->get();

        return view('comercios.index', compact('comercios'));
    }

    /**
     * GET /comercios/{comercio}
     *
     * Route Model Binding: Laravel convierte automáticamente el
     * parámetro {comercio} de la ruta en una instancia de Comercio
     * (o lanza 404 si no existe).
     *
     * load('transacciones') trae la relación en una sola consulta
     * adicional (no una por cada transacción), evitando el N+1.
     */
    public function show(Comercio $comercio): View
    {
        $comercio->load('transacciones');

        return view('comercios.show', compact('comercio'));
    }
}
