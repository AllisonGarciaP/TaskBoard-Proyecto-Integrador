{{-- Formulario Nueva Transacción --}}
@extends('layouts.app')

@section('titulo', 'Nueva transacción')

@section('contenido')
    <p><a href="{{ route('comercios.show', $comercio) }}">&larr; Volver al comercio</a></p>
    <h1>Nueva transacción</h1>
    <p class="meta">Comercio: {{ $comercio->nombre_comercio }}</p>

    @if ($errors->any())
        <div role="alert" style="color:#C23B32;">
            <p>Revisa los siguientes datos:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transacciones.store') }}" method="POST">
        @csrf
        <input type="hidden" name="comercio_id" value="{{ $comercio->id }}">

        <label for="cliente_nombre">Cliente</label>
        <input id="cliente_nombre" name="cliente_nombre" type="text" value="{{ old('cliente_nombre') }}" aria-invalid="{{ $errors->has('cliente_nombre') ? 'true' : 'false' }}">
        @error('cliente_nombre')
            <span class="error" style="color:#C23B32;">{{ $message }}</span>
        @enderror

        <label for="monto">Monto</label>
        <input id="monto" name="monto" type="number" step="0.01" value="{{ old('monto') }}" aria-invalid="{{ $errors->has('monto') ? 'true' : 'false' }}">
        @error('monto')
            <span class="error" style="color:#C23B32;">{{ $message }}</span>
        @enderror

        <button type="submit">Registrar</button>
    </form>
@endsection