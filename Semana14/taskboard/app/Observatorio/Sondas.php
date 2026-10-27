<?php

namespace App\Observatorio;

use App\Models\Telescopio;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Sondas del Parcial II.
 *
 * Cada sonda deja la base de datos en el estado que corresponde a la semilla, ejecuta una prueba
 * sobre el código del estudiante (modelos o peticiones HTTP reales) y devuelve UN número.
 * Ese número es la respuesta que se escribe en Moodle.
 *
 * NO MODIFICAR (parte de la infraestructura del parcial).
 */
final class Sondas
{
    public const CLAVE_NUEVOS = 'secreto123';

    /** @var array<string, array{0:int, 1:string, 2:bool}> clave => [tarea, descripción, necesita servidor] */
    public const CATALOGO = [
        'p01' => [1, 'Telescopios con 4 o más reservas (relaciones Eloquent)', false],
        'p02' => [1, 'Reservas confirmadas de un usuario (relación User → reservas)', false],
        'p03' => [2, 'Telescopios que muestra la vista /telescopios (Blade)', true],
        'p04' => [2, 'Total de reservas que muestra /telescopios (Blade)', true],
        'p05' => [2, 'Reservas que lista el detalle de un telescopio (Blade)', true],
        'p06' => [3, 'Envío del formulario de nueva reserva (POST + validación)', true],
        'p07' => [3, 'Telescopio preseleccionado en el formulario (GET + Blade)', true],
        'p08' => [3, 'Campos que fallan la validación', true],
        'p09' => [3, 'Longitud de los mensajes de error personalizados', true],
        'p10' => [4, 'Actualización de una reserva (CRUD: update)', true],
        'p11' => [4, 'Eliminación de reservas (CRUD: delete)', true],
        'p12' => [4, 'Cambio de estado de reservas (CRUD: mover)', true],
        'p13' => [5, 'Inicio de sesión y listado propio', true],
        'p14' => [5, 'Reservas a las que puede entrar un usuario', true],
        'p15' => [5, 'Registro e inicio de sesión (intentos que tienen éxito)', true],
    ];

    public static function necesitaServidor(string $clave): bool
    {
        return self::CATALOGO[$clave][2];
    }

    /** @param  callable():Cliente|null  $nuevoCliente */
    public static function ejecutar(string $clave, int $semilla, ?callable $nuevoCliente): int
    {
        if (! isset(self::CATALOGO[$clave])) {
            throw new InvalidArgumentException("Sonda desconocida: {$clave}");
        }
        Semilla::aplicar($semilla);

        return (int) self::{'sonda'.strtoupper($clave)}($semilla, $nuevoCliente);
    }

    // ---------------------------------------------------------------- utilidades

    private static function g(int $semilla, int $indice): Prng
    {
        return new Prng($semilla * 131 + $indice);
    }

    /** @return list<object> */
    private static function reservas(): array
    {
        return DB::table('reservas')->orderBy('id')->get()->all();
    }

    /** @return list<int> */
    private static function idsActivos(): array
    {
        return DB::table('telescopios')->where('activo', 1)->orderBy('id')->pluck('id')->map(fn ($v) => (int) $v)->all();
    }

    private static function contar(string $patron, string $texto): int
    {
        return (int) preg_match_all($patron, $texto);
    }

    // -------------------------------------------------------------------- tarea 1

    private static function sondaP01(int $semilla, $nuevo): int
    {
        return Telescopio::withCount('reservas')->get()->where('reservas_count', '>=', 4)->count();
    }

    private static function sondaP02(int $semilla, $nuevo): int
    {
        $usuario = User::where('email', 'ana@obs.test')->firstOrFail();

        return $usuario->reservas()->where('estado', 'confirmada')->count();
    }

    // -------------------------------------------------------------------- tarea 2

    private static function sondaP03(int $semilla, $nuevo): int
    {
        $cuerpo = $nuevo()->get('/telescopios')['cuerpo'];

        // Suma de los id de los telescopios que aparecen como fila.
        preg_match_all('/<tr[^>]*\sdata-telescopio="(\d+)"/', $cuerpo, $m);

        return array_sum(array_map('intval', $m[1]));
    }

    private static function sondaP04(int $semilla, $nuevo): int
    {
        $cuerpo = $nuevo()->get('/telescopios')['cuerpo'];

        return preg_match('/id="total-reservas"[^>]*>\s*(\d+)\s*</', $cuerpo, $m) ? (int) $m[1] : 0;
    }

    private static function sondaP05(int $semilla, $nuevo): int
    {
        $n = DB::table('telescopios')->count();
        $id = 1 + self::g($semilla, 5)->siguiente() % $n;
        $cuerpo = $nuevo()->get('/telescopios/'.$id)['cuerpo'];

        // Suma de los id de las reservas listadas.
        preg_match_all('/<li[^>]*\sdata-reserva="(\d+)"/', $cuerpo, $m);

        return array_sum(array_map('intval', $m[1]));
    }

    // -------------------------------------------------------------------- tarea 3

    private static function sondaP06(int $semilla, $nuevo): int
    {
        $g = self::g($semilla, 6);
        $activos = self::idsActivos();
        $telescopio = $activos[$g->siguiente() % count($activos)];
        $horas = 1 + $g->siguiente() % 6; // 1..4 válido, 5..6 inválido

        $c = $nuevo();
        $c->entrarComo(1 + $g->siguiente() % 3);
        $c->enviar('/reservas', [
            'telescopio_id' => $telescopio,
            'fecha' => '2026-12-05',
            'horas' => $horas,
            'objetivo' => 'Observación de Saturno',
        ]);

        return DB::table('reservas')->count();
    }

    private static function sondaP07(int $semilla, $nuevo): int
    {
        $g = self::g($semilla, 7);
        $activos = self::idsActivos();
        $telescopio = $activos[$g->siguiente() % count($activos)];

        $c = $nuevo();
        $c->entrarComo(1 + $g->siguiente() % 3);
        $cuerpo = $c->get('/reservas/create?telescopio='.$telescopio)['cuerpo'];

        return preg_match('/<option[^>]*value="(\d+)"[^>]*\sselected/', $cuerpo, $m) ? (int) $m[1] : 0;
    }

    private static function sondaP08(int $semilla, $nuevo): int
    {
        $g = self::g($semilla, 8);
        $activos = self::idsActivos();
        $mascara = 1 + $g->siguiente() % 15;

        $datos = [
            'telescopio_id' => $activos[0],
            'fecha' => '2026-12-01',
            'horas' => 3,
            'objetivo' => 'Observación de Marte',
        ];
        $invalidos = ['telescopio_id' => 9999, 'fecha' => '2026-10-01', 'horas' => 9, 'objetivo' => 'ab'];
        $i = 0;
        foreach ($invalidos as $campo => $valor) {
            if ($mascara & (1 << $i)) {
                $datos[$campo] = $valor;
            }
            $i++;
        }

        $c = $nuevo();
        $c->entrarComo(1 + $g->siguiente() % 3);
        $c->enviar('/reservas', $datos);

        // Bit i = 1 si el campo i (telescopio_id, fecha, horas, objetivo) tiene error.
        $errores = $c->errores();
        $resultado = 0;
        $i = 0;
        foreach (array_keys($invalidos) as $campo) {
            if (isset($errores[$campo])) {
                $resultado |= 1 << $i;
            }
            $i++;
        }

        return $resultado;
    }

    private static function sondaP09(int $semilla, $nuevo): int
    {
        $g = self::g($semilla, 9);
        $activos = self::idsActivos();
        $mascara = 1 + $g->siguiente() % 15;

        $datos = [
            'telescopio_id' => $activos[0],
            'fecha' => '2026-12-01',
            'horas' => 3,
            'objetivo' => 'Observación de Marte',
        ];
        $invalidos = ['objetivo' => '', 'horas' => 0, 'fecha' => '2026-10-01', 'telescopio_id' => 9999];
        $i = 0;
        foreach ($invalidos as $campo => $valor) {
            if ($mascara & (1 << $i)) {
                $datos[$campo] = $valor;
            }
            $i++;
        }

        $c = $nuevo();
        $c->entrarComo(1 + $g->siguiente() % 3);
        $c->enviar('/reservas', $datos);

        $suma = 0;
        foreach ($c->errores() as $mensajes) {
            $suma += mb_strlen((string) ($mensajes[0] ?? ''));
        }

        return $suma;
    }

    // -------------------------------------------------------------------- tarea 4

    private static function sondaP10(int $semilla, $nuevo): int
    {
        $todas = self::reservas();
        $r = $todas[self::g($semilla, 10)->siguiente() % count($todas)];

        $c = $nuevo();
        $c->entrarComo((int) $r->user_id);
        $c->enviar('/reservas/'.$r->id, [
            'fecha' => substr((string) $r->fecha, 0, 10),
            'horas' => ((int) $r->horas % 4) + 1,
            'objetivo' => 'Observación actualizada',
        ], 'PUT');

        return (int) DB::table('reservas')->where('telescopio_id', $r->telescopio_id)->sum('horas');
    }

    private static function sondaP11(int $semilla, $nuevo): int
    {
        $g = self::g($semilla, 11);
        $todas = self::reservas();
        $a = $g->siguiente() % count($todas);
        $b = ($a + 1 + $g->siguiente() % (count($todas) - 1)) % count($todas);

        foreach ([$a, $b] as $indice) {
            $r = $todas[$indice];
            $c = $nuevo();
            $c->entrarComo((int) $r->user_id);
            $c->enviar('/reservas/'.$r->id, [], 'DELETE');
        }

        return DB::table('reservas')->count();
    }

    private static function sondaP12(int $semilla, $nuevo): int
    {
        $pendientes = DB::table('reservas')->where('estado', 'pendiente')->orderBy('id')->limit(3)->get();

        foreach ($pendientes as $r) {
            $c = $nuevo();
            $c->entrarComo((int) $r->user_id);
            $c->enviar('/reservas/'.$r->id.'/estado', ['estado' => 'confirmada'], 'PATCH');
        }

        return DB::table('reservas')->where('estado', 'confirmada')->count();
    }

    // -------------------------------------------------------------------- tarea 5

    private static function sondaP13(int $semilla, $nuevo): int
    {
        $g = self::g($semilla, 13);
        [, $correo] = Semilla::USUARIOS[$g->siguiente() % 3];

        $c = $nuevo();
        $c->enviar('/login', ['email' => $correo, 'password' => Semilla::CLAVE_USUARIOS]);
        $lista = $c->get('/reservas');

        return $lista['estado'] === 200 ? self::contar('/<tr[^>]*\sdata-reserva=/', $lista['cuerpo']) : 0;
    }

    private static function sondaP14(int $semilla, $nuevo): int
    {
        $usuario = 1 + self::g($semilla, 14)->siguiente() % 3;

        $c = $nuevo();
        $c->entrarComo($usuario);
        $suma = 0;
        foreach (self::reservas() as $r) {
            // Suma de los id de las reservas a las que el usuario logra entrar (respuesta 200).
            if ($c->get('/reservas/'.$r->id)['estado'] === 200) {
                $suma += (int) $r->id;
            }
        }

        return $suma;
    }

    private static function sondaP15(int $semilla, $nuevo): int
    {
        $mascara = 1 + self::g($semilla, 15)->siguiente() % 62;
        $exitos = 0;

        for ($i = 0; $i < 6; $i++) {
            $entra = $nuevo();
            if ($mascara & (1 << $i)) {
                $correo = "nuevo{$semilla}_{$i}@obs.test";
                $nuevo()->enviar('/register', [
                    'name' => 'Usuario Nuevo '.$i,
                    'email' => $correo,
                    'password' => self::CLAVE_NUEVOS,
                    'password_confirmation' => self::CLAVE_NUEVOS,
                ]);
                $entra->enviar('/login', ['email' => $correo, 'password' => self::CLAVE_NUEVOS]);
            } else {
                $entra->enviar('/login', ['email' => Semilla::USUARIOS[$i % 3][1], 'password' => 'incorrecta1']);
            }
            if ($entra->get('/reservas')['estado'] === 200) {
                $exitos |= 1 << $i;
            }
        }

        return $exitos;
    }
}
