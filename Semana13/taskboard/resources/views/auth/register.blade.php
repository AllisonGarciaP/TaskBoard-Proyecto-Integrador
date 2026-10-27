@extends('layouts.app')

@section('titulo', 'Registro')

@section('contenido')
    <h1>Crear cuenta</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label>Nombre
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Correo
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Contraseña (mínimo 8 caracteres)
            <input type="password" name="password">
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Confirmar contraseña
            <input type="password" name="password_confirmation">
        </label>
        <button class="accion" type="submit">Registrarme</button>
    </form>
@endsection
