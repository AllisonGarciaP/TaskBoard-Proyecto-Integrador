<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;

class ReservaApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Reserva::query()->with('telescopio')->orderBy('id');

        if ($request->query->has('estado')) {
            $validated = $request->validate(['estado' => ['required', 'string', 'in:pendiente,confirmada,cancelada']]);
            $query->where('estado', $validated['estado']);
        }

        return ReservaResource::collection($query->get());
    }

    public function show(Reserva $reserva)
    {
        return new ReservaResource($reserva->load('telescopio'));
    }

    public function store(StoreReservaRequest $request)
    {
        $userData = validator($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ])->validate();

        $reserva = Reserva::create([
            ...$request->validated(),
            'user_id' => $userData['user_id'],
            'estado' => 'pendiente',
        ]);

        return (new ReservaResource($reserva->load('telescopio')))->response()->setStatusCode(201);
    }

    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return response()->noContent();
    }
}
