<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Models\Reserva;
use App\Models\Telescopio;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = auth()->user()->reservas()->with('telescopio')->orderBy('id')->get();

        return view('reservas.index', compact('reservas'));
    }

    public function create()
    {
        $telescopios = Telescopio::where('activo', true)->orderBy('id')->get();

        return view('reservas.create', compact('telescopios'));
    }

    public function store(StoreReservaRequest $request)
    {
        $request->user()->reservas()->create([
            ...$request->validated(),
            'estado' => 'pendiente',
        ]);

        return redirect()->route('reservas.index')->with('ok', 'Reserva creada.');
    }

    public function show(Reserva $reserva)
    {
        $this->autorizar($reserva);

        return view('reservas.show', compact('reserva'));
    }

    public function edit(Reserva $reserva)
    {
        $this->autorizar($reserva);

        return view('reservas.edit', compact('reserva'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $this->autorizar($reserva);

        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'horas' => ['required', 'integer', 'between:1,4'],
            'objetivo' => ['required', 'string', 'min:3', 'max:60'],
        ]);

        // TODO (Tarea 4): actualiza la reserva con $datos.

        return redirect()->route('reservas.index')->with('ok', 'Reserva actualizada.');
    }

    public function destroy(Reserva $reserva)
    {
        $this->autorizar($reserva);

        $reserva->delete();

        return redirect()->route('reservas.index')->with('ok', 'Reserva eliminada.');
    }

    public function cambiarEstado(Request $request, Reserva $reserva)
    {
        $this->autorizar($reserva);

        // TODO (Tarea 4): valida que 'estado' sea pendiente, confirmada o cancelada y guarda el cambio.

        return redirect()->route('reservas.index')->with('ok', 'Estado actualizado.');
    }

    /** Solo el dueño de la reserva puede verla o modificarla. */
    private function autorizar(Reserva $reserva): void
    {
        abort_unless($reserva->user_id === auth()->id(), 403);
    }
}
