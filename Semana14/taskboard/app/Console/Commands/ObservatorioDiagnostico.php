<?php

namespace App\Console\Commands;

use App\Observatorio\Cliente;
use App\Observatorio\Semilla;
use App\Observatorio\ServidorLocal;
use Illuminate\Console\Command;
use Throwable;

class ObservatorioDiagnostico extends Command
{
    protected $signature = 'observatorio:diagnostico';

    protected $description = 'Comprueba que tu equipo está listo para el Parcial II (ejecútalo el día anterior)';

    public function handle(): int
    {
        $fallos = 0;
        $revisar = function (string $nombre, callable $prueba) use (&$fallos) {
            try {
                $detalle = $prueba();
                $this->line("  [OK]    {$nombre}".($detalle ? " ({$detalle})" : ''));
            } catch (Throwable $e) {
                $fallos++;
                $this->line("  [FALLA] {$nombre}: ".$e->getMessage());
            }
        };

        $this->info('Diagnóstico del entorno — Parcial II');

        $revisar('PHP 8.2 o superior', function () {
            if (version_compare(PHP_VERSION, '8.2.0', '<')) {
                throw new \RuntimeException('Tienes PHP '.PHP_VERSION);
            }

            return 'PHP '.PHP_VERSION;
        });
        $revisar('Extensión pdo_sqlite', function () {
            if (! extension_loaded('pdo_sqlite')) {
                throw new \RuntimeException('Activa pdo_sqlite en tu php.ini');
            }
        });
        $revisar('Carpeta vendor (dependencias)', function () {
            if (! is_file(base_path('vendor/autoload.php'))) {
                throw new \RuntimeException('Falta vendor. Ejecuta: composer install');
            }
        });
        $revisar('Base de datos SQLite escribible', function () {
            Semilla::aplicar(1);
        });
        $revisar('Servidor local y peticiones HTTP', function () {
            $servidor = ServidorLocal::iniciar();
            try {
                $respuesta = (new Cliente($servidor->url()))->get('/_sonda/token');
                if ($respuesta['estado'] !== 200) {
                    throw new \RuntimeException('Respuesta '.$respuesta['estado']);
                }
            } finally {
                $servidor->detener();
            }
        });

        $this->newLine();
        if ($fallos === 0) {
            $this->info('LISTO: tu equipo puede ejecutar las sondas del parcial.');

            return self::SUCCESS;
        }
        $this->error("Hay {$fallos} problema(s). Corrígelos antes del parcial.");

        return self::FAILURE;
    }
}
