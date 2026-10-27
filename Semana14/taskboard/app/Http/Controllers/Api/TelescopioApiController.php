<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TelescopioResource;
use App\Models\Telescopio;
use Illuminate\Http\Request;

class TelescopioApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Telescopio::query()->withCount('reservas')->orderBy('id');

        if ($request->query->has('activo')) {
            $validated = $request->validate(['activo' => ['required', 'boolean']]);
            $query->where('activo', $validated['activo']);
        }

        return TelescopioResource::collection($query->get());
    }

    public function show(Telescopio $telescopio)
    {
        return new TelescopioResource($telescopio->loadCount('reservas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:255'],
            'apertura_mm' => ['required', 'integer', 'between:1,65535'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $telescopio = Telescopio::create($data);

        return (new TelescopioResource($telescopio->loadCount('reservas')))->response()->setStatusCode(201);
    }

    public function destroy(Telescopio $telescopio)
    {
        if ($telescopio->reservas()->exists()) {
            return response()->json(['message' => 'No se puede eliminar un telescopio que tiene reservas.'], 409);
        }

        $telescopio->delete();

        return response()->noContent();
    }
}
