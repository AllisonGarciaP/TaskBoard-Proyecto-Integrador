@extends('layouts.app')

@section('titulo', 'Editar reserva')

@section('contenido')
    <h1>Editar reserva</h1>
    <p>Telescopio: <strong>{{ $reserva->telescopio->nombre }}</strong></p>

    <form method="POST" action="{{ route('reservas.update', $reserva) }}">
        @csrf
        @method('PUT')

        <label>Fecha
            <input type="date" name="fecha" value="{{ old('fecha', $reserva->fecha->format('Y-m-d')) }}">
            @error('fecha') <span class="error">{{ $message }}</span> @enderror
        </label>

        <label>Horas (1 a 4)
            <input type="number" name="horas" value="{{ old('horas', $reserva->horas) }}">
            @error('horas') <span class="error">{{ $message }}</span> @enderror
        </label>

        <label>Objetivo
            <input type="text" name="objetivo" value="{{ old('objetivo', $reserva->objetivo) }}">
            @error('objetivo') <span class="error">{{ $message }}</span> @enderror
        </label>

        <button class="accion" type="submit">Guardar cambios</button>
    </form>
@endsection
