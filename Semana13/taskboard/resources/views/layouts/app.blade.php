<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') · Observatorio</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6fb; color: #1b2433; }
        nav { background: #14213d; padding: .8rem 1.2rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        nav a, nav button { color: #fff; text-decoration: none; background: none; border: 0; font: inherit; cursor: pointer; }
        main { max-width: 900px; margin: 1.5rem auto; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: .5rem .6rem; border-bottom: 1px solid #dde3ee; text-align: left; }
        th { background: #e8edf6; }
        .ok { background: #dff5e1; padding: .6rem 1rem; border-radius: 6px; }
        .error { color: #b00020; font-size: .9rem; display: block; }
        label { display: block; margin: .8rem 0; }
        input, select { padding: .4rem; min-width: 260px; }
        button.accion { padding: .35rem .7rem; cursor: pointer; }
        form.en-linea { display: inline; }
    </style>
</head>
<body>
<nav>
    <strong style="color:#fca311">Observatorio</strong>
    <a href="{{ route('telescopios.index') }}">Telescopios</a>
    @auth
        <a href="{{ route('reservas.index') }}">Mis reservas</a>
        <a href="{{ route('reservas.create') }}">Nueva reserva</a>
        <form method="POST" action="{{ route('logout') }}" class="en-linea">
            @csrf
            <button type="submit">Salir ({{ auth()->user()->name }})</button>
        </form>
    @else
        <a href="{{ route('login') }}">Entrar</a>
        <a href="{{ route('register') }}">Registrarse</a>
    @endauth
</nav>
<main>
    @if (session('ok'))
        <p class="ok">{{ session('ok') }}</p>
    @endif
    @yield('contenido')
</main>
</body>
</html>
