@extends('layouts.app')

@section('titulo', 'Reserva')

@section('contenido')
    <h1>Reserva #{{ $reserva->id }}</h1>
    <ul>
        <li>Telescopio: {{ $reserva->telescopio->nombre }}</li>
        <li>Fecha: {{ $reserva->fecha->format('d/m/Y') }}</li>
        <li>Horas: {{ $reserva->horas }}</li>
        <li>Objetivo: {{ $reserva->objetivo }}</li>
        <li>Estado: {{ $reserva->estado }}</li>
    </ul>
    <p><a href="{{ route('reservas.index') }}">&larr; Volver</a></p>
@endsection
