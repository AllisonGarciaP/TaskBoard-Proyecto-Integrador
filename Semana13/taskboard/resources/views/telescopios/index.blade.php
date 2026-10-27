@extends('layouts.app')

@section('titulo', 'Telescopios')

@section('contenido')
    <h1>Telescopios del observatorio</h1>

    <table>
        <thead>
            <tr><th>Nombre</th><th>Tipo</th><th>Apertura</th><th>Reservas</th><th></th></tr>
        </thead>
        <tbody>
            @php($total = 0)
            @foreach ($telescopios as $t)
                @if ($t->activo)
                    @php($total += $t->reservas_count)
                    <tr data-telescopio="{{ $t->id }}">
                        <td>{{ $t->nombre }}</td>
                        <td>{{ $t->tipo }}</td>
                        <td>{{ $t->apertura_mm }} mm</td>
                        <td>{{ $t->reservas_count }}</td>
                        <td><a href="{{ route('telescopios.show', $t) }}">Ver detalle</a></td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <p>Total de reservas: <strong id="total-reservas">{{ $total ?? 0 }}</strong></p>
@endsection
