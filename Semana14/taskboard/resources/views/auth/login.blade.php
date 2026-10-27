@extends('layouts.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <h1>Iniciar sesión</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Correo
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Contraseña
            <input type="password" name="password">
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </label>
        <button class="accion" type="submit">Entrar</button>
    </form>
@endsection
