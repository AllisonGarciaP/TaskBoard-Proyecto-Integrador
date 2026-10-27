@extends('layouts.app')

@section('titulo', 'Nueva reserva')

@section('contenido')
    <h1>Nueva reserva</h1>

    <form method="POST" action="{{ route('reservas.store') }}">
        @csrf

        <label>Telescopio
            <select name="telescopio_id">
                @foreach ($telescopios as $t)
                    <option value="{{ $t->id }}" @selected(old('telescopio_id', request('telescopio')) == $t->id)>{{ $t->nombre }}</option>
                @endforeach
            </select>
            @error('telescopio_id') <span class="error">{{ $message }}</span> @enderror
        </label>

        <label>Fecha
            <input type="date" name="fecha" value="{{ old('fecha') }}">
            @error('fecha') <span class="error">{{ $message }}</span> @enderror
        </label>

        <label>Horas (1 a 4)
            <input type="number" name="horas" value="{{ old('horas') }}">
            @error('horas') <span class="error">{{ $message }}</span> @enderror
        </label>

        <label>Objetivo de la observación
            <input type="text" name="objetivo" value="{{ old('objetivo') }}">
            @error('objetivo') <span class="error">{{ $message }}</span> @enderror
        </label>

        <button class="accion" type="submit">Reservar</button>
    </form>
@endsection
