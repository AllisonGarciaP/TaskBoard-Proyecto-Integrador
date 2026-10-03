{{-- Sandbox de formularios HTML y token CSRF --}}
@extends('layouts.app')

@section('titulo', 'Sandbox de formulario')

@section('contenido')
    <h1>Laboratorio de formularios</h1>

    <form action="{{ route('practica.enviar') }}" method="POST">
        @csrf

        <label for="cliente">Cliente</label>
        <input id="cliente" name="cliente_nombre" type="text">

        <label for="correo">Correo de contacto</label>
        <input id="correo" name="correo_contacto" type="email">

        <label for="monto">Monto</label>
        <input id="monto" name="monto" type="number" step="0.01">

        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="Iniciada">Iniciada</option>
            <option value="Completada">Completada</option>
        </select>

        <label for="recurrente">¿Es una transacción recurrente?</label>
        <input id="recurrente" name="recurrente" type="checkbox" value="1">

        <button type="submit">Enviar (modo prueba)</button>
    </form>
@endsection