@extends('layouts.app')

@section('titulo', 'Reservas')

@section('contenido')
    <h1>Reservas</h1>

    <table>
        <thead>
            <tr><th>Telescopio</th><th>Fecha</th><th>Horas</th><th>Objetivo</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            @forelse ($reservas as $r)
                <tr data-reserva="{{ $r->id }}">
                    <td>{{ $r->telescopio->nombre }}</td>
                    <td>{{ $r->fecha->format('d/m/Y') }}</td>
                    <td>{{ $r->horas }}</td>
                    <td>{{ $r->objetivo }}</td>
                    <td>
                        <form method="POST" action="{{ route('reservas.estado', $r) }}" class="en-linea">
                            @csrf
                            @method('PATCH')
                            <select name="estado" style="min-width:0">
                                @foreach (['pendiente', 'confirmada', 'cancelada'] as $e)
                                    <option value="{{ $e }}" @selected($r->estado === $e)>{{ $e }}</option>
                                @endforeach
                            </select>
                            <button class="accion" type="submit">Cambiar</button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('reservas.show', $r) }}">Ver</a> ·
                        <a href="{{ route('reservas.edit', $r) }}">Editar</a>
                        <form method="POST" action="{{ route('reservas.destroy', $r) }}" class="en-linea"
                              onsubmit="return confirm('¿Eliminar esta reserva?')">
                            @csrf
                            @method('DELETE')
                            <button class="accion" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No hay reservas para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
