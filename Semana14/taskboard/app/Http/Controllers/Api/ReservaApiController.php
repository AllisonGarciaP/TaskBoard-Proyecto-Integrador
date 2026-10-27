<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaApiController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->reservas()->with('telescopio')->orderBy('id');

        if ($request->query->has('estado')) {
            $validated = $request->validate(['estado' => ['required', 'string', 'in:pendiente,confirmada,cancelada']]);
            $query->where('estado', $validated['estado']);
        }

        return ReservaResource::collection($query->get());
    }

    public function show(Request $request, Reserva $reserva)
    {
        $reserva = $request->user()->reservas()->with('telescopio')->findOrFail($reserva->getKey());

        return new ReservaResource($reserva);
    }

    public function store(StoreReservaRequest $request)
    {
        $reserva = $request->user()->reservas()->create([
            ...$request->validated(),
            'estado' => 'pendiente',
        ]);

        return (new ReservaResource($reserva->load('telescopio')))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Reserva $reserva)
    {
        $reserva = $request->user()->reservas()->findOrFail($reserva->getKey());
        $reserva->delete();

        return response()->noContent();
    }
}
