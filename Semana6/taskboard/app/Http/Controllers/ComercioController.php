<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use Illuminate\Database\Eloquent\Collection;

class ComercioController extends Controller
{
    public function index(): Collection
    {
        return Comercio::with('transacciones')->orderBy('id')->get();
    }

    public function show(Comercio $comercio): Comercio
    {
        return $comercio->load('transacciones');
    }
}