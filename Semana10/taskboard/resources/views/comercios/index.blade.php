{{-- Semana 7 · Blade --}}
{{-- resources/views/comercios/index.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Comercios afiliados')

@section('contenido')
    <h1>Comercios afiliados a la pasarela</h1>

    <form action="{{ route('comercios.index') }}" method="GET">
        <label for="buscar">Buscar comercio</label>
        <input id="buscar" type="text" name="buscar" placeholder="Buscar comercio..." value="{{ request('buscar') }}">

        <label for="rubro">Rubro</label>
        <select id="rubro" name="rubro">
            <option value="">Todos los rubros</option>
            <option value="Restaurante" @selected(request('rubro') === 'Restaurante')>Restaurante</option>
            <option value="Ferretería" @selected(request('rubro') === 'Ferretería')>Ferretería</option>
        </select>

        <button type="submit">Buscar</button>
    </form>

    <ul class="comercios">
        @forelse ($comercios as $comercio)
            <li class="card">
                <a href="{{ route('comercios.show', $comercio) }}">
                    {{ $comercio->nombre_comercio }}
                </a>
                <span class="meta">— {{ $comercio->rubro }}
                    ({{ $comercio->transacciones_count }} transacciones)</span>
                <br>
                <x-badge-actividad :totalTransacciones="$comercio->transacciones_count" />
            </li>
        @empty
            <li class="card">Aún no hay comercios afiliados.</li>
        @endforelse
    </ul>
@endsection
