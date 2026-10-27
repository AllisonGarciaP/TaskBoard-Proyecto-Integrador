<?php

namespace App\Observatorio;

use RuntimeException;

/**
 * Levanta temporalmente un servidor PHP local (como `php artisan serve`) para que las sondas
 * puedan hacer peticiones HTTP reales a la aplicación del estudiante.
 *
 * NO MODIFICAR (parte de la infraestructura del parcial).
 */
final class ServidorLocal
{
    /** @var resource|null */
    private $proceso = null;

    private int $puerto = 0;

    public static function iniciar(): self
    {
        $servidor = new self();
        $servidor->arrancar();

        return $servidor;
    }

    public function url(): string
    {
        return 'http://127.0.0.1:'.$this->puerto;
    }

    private function arrancar(): void
    {
        $socket = @stream_socket_server('tcp://127.0.0.1:0', $codigo, $mensaje);
        if ($socket === false) {
            throw new RuntimeException("No se pudo reservar un puerto local: $mensaje");
        }
        $nombre = stream_socket_get_name($socket, false);
        fclose($socket);
        $this->puerto = (int) substr(strrchr($nombre, ':'), 1);

        $router = base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');
        if (! is_file($router)) {
            throw new RuntimeException('No se encontró vendor/laravel/framework/.../server.php. ¿Existe la carpeta vendor?');
        }

        $log = storage_path('logs/servidor-sonda.log');
        $descriptores = [
            0 => ['pipe', 'r'],
            1 => ['file', $log, 'a'],
            2 => ['file', $log, 'a'],
        ];
        $entorno = array_merge(getenv(), [
            'OBS_SONDA' => '1',
            'PHP_CLI_SERVER_WORKERS' => '1',
        ]);

        $this->proceso = proc_open(
            [PHP_BINARY, '-S', '127.0.0.1:'.$this->puerto, $router],
            $descriptores,
            $pipes,
            public_path(),
            $entorno
        );
        if (! is_resource($this->proceso)) {
            throw new RuntimeException('No se pudo iniciar el servidor PHP local.');
        }

        // Espera a que responda.
        $limite = microtime(true) + 20;
        while (microtime(true) < $limite) {
            $conexion = @fsockopen('127.0.0.1', $this->puerto, $e1, $e2, 0.2);
            if ($conexion) {
                fclose($conexion);

                return;
            }
            usleep(100000);
        }
        $this->detener();
        throw new RuntimeException('El servidor PHP local no respondió a tiempo.');
    }

    public function detener(): void
    {
        if (is_resource($this->proceso)) {
            proc_terminate($this->proceso);
            proc_close($this->proceso);
            $this->proceso = null;
        }
    }

    public function __destruct()
    {
        $this->detener();
    }
}
