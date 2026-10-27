<?php

namespace App\Observatorio;

use DateTimeImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Deja la base de datos en un estado conocido que depende solo del número de semilla.
 * Inserta con DB::table() a propósito: no usa los modelos del estudiante.
 *
 * NO MODIFICAR (parte de la infraestructura del parcial).
 */
final class Semilla
{
    public const CLAVE_USUARIOS = 'clave1234';

    // Hash bcrypt (costo 12) de CLAVE_USUARIOS.
    private const HASH = '$2y$12$V6jP7CfA7IIzaS7ni.aNI.2Uy1SjpZn2gCY9rh6Yk8fuKFt14Wle.';

    public const USUARIOS = [
        ['Ana Ruiz', 'ana@obs.test'],
        ['Luis Mena', 'luis@obs.test'],
        ['Marta Cruz', 'marta@obs.test'],
    ];

    private const NOMBRES = [
        'Orión 80', 'Vega 102', 'Altair 130', 'Sirio 150', 'Rigel 200',
        'Polaris 70', 'Antares 254', 'Capella 114', 'Deneb 90', 'Castor 127',
    ];

    private const TIPOS = ['Refractor', 'Reflector', 'Catadióptrico'];

    private const APERTURAS = [70, 80, 102, 130, 150, 200, 254];

    private const OBJETIVOS = [
        'Júpiter y sus lunas', 'Anillos de Saturno', 'Nebulosa de Orión', 'Cúmulo de las Pléyades',
        'Galaxia de Andrómeda', 'Cráteres lunares', 'Fases de Venus', 'Nebulosa del Cangrejo',
        'Doble cúmulo de Perseo', 'Cúmulo globular M13',
    ];

    public const ESTADOS = ['pendiente', 'confirmada', 'cancelada'];

    public static function aplicar(int $semilla): void
    {
        self::prepararTablas();

        $g = new Prng($semilla);
        $ahora = now()->toDateTimeString();

        foreach (self::USUARIOS as [$nombre, $correo]) {
            DB::table('users')->insert([
                'name' => $nombre, 'email' => $correo, 'password' => self::HASH,
                'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }

        // Telescopios (6 a 10) con algunos inactivos.
        $n = 6 + $g->entre(0, 4);
        $activos = [];
        for ($i = 0; $i < $n; $i++) {
            $activos[$i] = $g->entre(1, 100) <= 70;
        }
        // Garantiza al menos 3 activos y 2 inactivos.
        for ($i = 0; $i < 3; $i++) {
            if (count(array_filter($activos)) >= 3) {
                break;
            }
            $activos[$i] = true;
        }
        for ($i = $n - 1; $i >= 0; $i--) {
            if (count($activos) - count(array_filter($activos)) >= 2) {
                break;
            }
            $activos[$i] = false;
        }

        for ($i = 0; $i < $n; $i++) {
            DB::table('telescopios')->insert([
                'nombre' => self::NOMBRES[$i],
                'tipo' => self::TIPOS[$g->entre(0, 2)],
                'apertura_mm' => self::APERTURAS[$g->entre(0, 6)],
                'activo' => $activos[$i] ? 1 : 0,
                'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }

        // Reservas (1 a 6 por telescopio), repartidas entre los 3 usuarios.
        $inicio = new DateTimeImmutable('2026-11-01');
        $reservas = [];
        for ($t = 1; $t <= $n; $t++) {
            $k = $g->entre(1, 6);
            for ($j = 0; $j < $k; $j++) {
                $reservas[] = [
                    'user_id' => $g->entre(1, 3),
                    'telescopio_id' => $t,
                    'fecha' => $inicio->modify('+'.$g->entre(0, 60).' days')->format('Y-m-d'),
                    'horas' => $g->entre(1, 4),
                    'objetivo' => self::OBJETIVOS[$g->entre(0, 9)],
                    'estado' => self::ESTADOS[$g->entre(0, 2)],
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ];
            }
        }
        // Garantiza que cada usuario tenga al menos una reserva.
        for ($u = 1; $u <= 3; $u++) {
            if (count(array_filter($reservas, fn ($r) => $r['user_id'] === $u)) === 0) {
                $reservas[$u - 1]['user_id'] = $u;
            }
        }
        DB::table('reservas')->insert($reservas);
    }

    private static function prepararTablas(): void
    {
        if (! Schema::hasTable('telescopios') || ! Schema::hasTable('users') || ! Schema::hasTable('sessions')) {
            Artisan::call('migrate', ['--force' => true]);
        }

        foreach (['reservas', 'telescopios', 'sessions', 'users'] as $tabla) {
            DB::table($tabla)->delete();
        }
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DELETE FROM sqlite_sequence');
        }
    }
}
