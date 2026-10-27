<?php

namespace App\Console\Commands;

use App\Observatorio\Cliente;
use App\Observatorio\Sondas;
use App\Observatorio\ServidorLocal;
use Illuminate\Console\Command;
use Throwable;

class ObservatorioProbar extends Command
{
    protected $signature = 'observatorio:probar {clave? : Sonda a ejecutar (p01 … p15)} {semilla? : Número de semilla que te indica Moodle} {--lista : Muestra todas las sondas}';

    protected $description = 'Ejecuta una sonda del Parcial II sobre tu aplicación y muestra el número que debes escribir en Moodle';

    public function handle(): int
    {
        if ($this->option('lista') || ! $this->argument('clave')) {
            $this->table(['Sonda', 'Tarea', 'Qué comprueba'], collect(Sondas::CATALOGO)->map(
                fn ($datos, $clave) => [$clave, $datos[0], $datos[1]]
            )->values()->all());
            $this->line('Uso: php artisan observatorio:probar p01 12345');

            return self::SUCCESS;
        }

        $clave = strtolower((string) $this->argument('clave'));
        if (! isset(Sondas::CATALOGO[$clave])) {
            $this->error("La sonda «{$clave}» no existe. Usa --lista para ver las disponibles.");

            return self::FAILURE;
        }
        if ($this->argument('semilla') === null || ! ctype_digit((string) $this->argument('semilla'))) {
            $this->error('Indica la semilla que aparece en la pregunta de Moodle, por ejemplo: php artisan observatorio:probar '.$clave.' 12345');

            return self::FAILURE;
        }
        $semilla = (int) $this->argument('semilla');
        [$tarea, $descripcion] = Sondas::CATALOGO[$clave];

        $this->line("Sonda {$clave} · semilla {$semilla} · Tarea {$tarea}: {$descripcion}");

        $servidor = null;
        try {
            $nuevo = null;
            if (Sondas::necesitaServidor($clave)) {
                $servidor = ServidorLocal::iniciar();
                $nuevo = fn () => new Cliente($servidor->url());
            }
            $resultado = Sondas::ejecutar($clave, $semilla, $nuevo);
        } catch (Throwable $e) {
            $this->newLine();
            $this->error("La sonda no pudo completarse. Revisa la Tarea {$tarea}.");
            $this->line('Detalle: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            $servidor?->detener();
        }

        $this->newLine();
        $this->info("RESULTADO: {$resultado}   <- escribe este número en Moodle");

        return self::SUCCESS;
    }
}
