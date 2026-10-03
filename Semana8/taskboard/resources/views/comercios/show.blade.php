{{-- Semana 8 · Ejemplo guiado integrador --}}
{{-- resources/views/comercios/show.blade.php --}}
@extends('layouts.app')

@section('titulo', $comercio->nombre_comercio)

@section('contenido')
    <p><a href="{{ route('comercios.index') }}">&larr; Volver a comercios</a></p>

    <h1>{{ $comercio->nombre_comercio }}</h1>
    <p class="meta">
        Rubro: {{ $comercio->rubro }}
        &middot;
        Teléfono: {{ $comercio->telefono ?? 'Sin teléfono registrado' }}
    </p>

    @if ($comercio->transacciones->count() === 0)
        <p class="meta">Este comercio es nuevo, aún no registra actividad.</p>
    @elseif ($comercio->transacciones->count() === 1)
        <p class="meta">Este comercio tiene su primera transacción registrada.</p>
    @else
        <p class="meta">Este comercio tiene un historial de {{ $comercio->transacciones->count() }} transacciones.</p>
    @endif

    <h2>Transacciones</h2>

    @forelse ($comercio->transacciones as $transaccion)
        <div class="card">
            <strong>${{ number_format($transaccion->monto, 2) }}</strong>
            — {{ $transaccion->cliente_nombre }}
            <x-badge-estado :estado="$transaccion->estado" />
        </div>
    @empty
        <p>Sin transacciones</p>
    @endforelse
@endsection
