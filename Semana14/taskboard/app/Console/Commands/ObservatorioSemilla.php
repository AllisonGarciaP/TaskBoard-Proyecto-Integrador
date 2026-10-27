<?php

namespace App\Console\Commands;

use App\Observatorio\Semilla;
use Illuminate\Console\Command;

class ObservatorioSemilla extends Command
{
    protected $signature = 'observatorio:semilla {numero : Número de semilla que te indica Moodle}';

    protected $description = 'Reinicia la base de datos con los datos que corresponden a una semilla';

    public function handle(): int
    {
        $numero = (int) $this->argument('numero');
        Semilla::aplicar($numero);

        $this->info("Base de datos reiniciada con la semilla {$numero}.");
        $this->line('Usuarios de prueba: ana@obs.test, luis@obs.test, marta@obs.test  (clave: '.Semilla::CLAVE_USUARIOS.')');

        return self::SUCCESS;
    }
}
