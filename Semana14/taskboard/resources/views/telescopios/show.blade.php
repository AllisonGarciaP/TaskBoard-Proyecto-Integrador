@extends('layouts.app')

@section('titulo', $telescopio->nombre)

@section('contenido')
    <h1>{{ $telescopio->nombre }}</h1>
    <p>{{ $telescopio->tipo }} · {{ $telescopio->apertura_mm }} mm · {{ $telescopio->activo ? 'Activo' : 'Inactivo' }}</p>

    <h2>Reservas de este telescopio</h2>
    <ul>
        @forelse ($telescopio->reservas as $r)
            <li data-reserva="{{ $r->id }}">{{ $r->fecha->format('Y-m-d') }} · {{ $r->horas }} h · {{ $r->objetivo }} ({{ $r->estado }}) — {{ $r->user->name }}</li>
        @empty
            <li>No hay reservas para este telescopio.</li>
        @endforelse
    </ul>

    <p><a href="{{ route('telescopios.index') }}">&larr; Volver</a></p>
@endsection
