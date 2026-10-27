<?php

namespace App\Observatorio;

use GuzzleHttp\Client;
use RuntimeException;

/**
 * "Navegador" de pruebas: conserva cookies entre peticiones (como un navegador real).
 * Un objeto Cliente nuevo equivale a abrir una ventana de incógnito nueva.
 *
 * NO MODIFICAR (parte de la infraestructura del parcial).
 */
final class Cliente
{
    private Client $http;

    private string $token = '';

    public function __construct(string $base)
    {
        $this->http = new Client([
            'base_uri' => $base,
            'cookies' => true,
            'allow_redirects' => false,
            'http_errors' => false,
            'timeout' => 40,
            'headers' => ['Accept' => 'text/html,application/xhtml+xml'],
        ]);
    }

    /** @return array{estado:int, cuerpo:string, ubicacion:?string} */
    public function get(string $ruta): array
    {
        return $this->resultado($ruta, $this->http->get($ruta));
    }

    /**
     * Envía un formulario con token CSRF. $metodo permite simular PUT/PATCH/DELETE como hace un navegador.
     *
     * @param  array<string, mixed>  $datos
     * @return array{estado:int, cuerpo:string, ubicacion:?string}
     */
    public function enviar(string $ruta, array $datos = [], string $metodo = 'POST'): array
    {
        if ($this->token === '') {
            $this->token = trim($this->get('/_sonda/token')['cuerpo']);
        }
        $datos['_token'] = $this->token;
        if ($metodo !== 'POST') {
            $datos['_method'] = $metodo;
        }

        return $this->resultado($ruta, $this->http->post($ruta, ['form_params' => $datos]));
    }

    /** Inicia sesión como el usuario indicado sin usar el código de login del estudiante. */
    public function entrarComo(int $idUsuario): void
    {
        $this->token = trim($this->get('/_sonda/entrar/'.$idUsuario)['cuerpo']);
    }

    /** @return array<string, list<string>> errores de validación de la última petición */
    public function errores(): array
    {
        $respuesta = $this->get('/_sonda/errores');

        return json_decode($respuesta['cuerpo'], true) ?: [];
    }

    private static function ultimoErrorDelLog(): string
    {
        $archivo = storage_path('logs/laravel.log');
        if (! is_file($archivo)) {
            return 'revisa storage/logs/laravel.log';
        }
        $texto = (string) file_get_contents($archivo, false, null, max(0, filesize($archivo) - 30000));
        $posicion = strrpos($texto, '.ERROR: ');
        if ($posicion === false) {
            return 'revisa storage/logs/laravel.log';
        }
        $linea = strtok(substr($texto, $posicion + 8), "\n");

        return mb_substr((string) $linea, 0, 260);
    }

    /** @return array{estado:int, cuerpo:string, ubicacion:?string} */
    private function resultado(string $ruta, $respuesta): array
    {
        $estado = $respuesta->getStatusCode();
        $cuerpo = (string) $respuesta->getBody();

        if ($estado >= 500) {
            throw new RuntimeException("La aplicación respondió {$estado} en {$ruta}. Causa: ".self::ultimoErrorDelLog());
        }

        return [
            'estado' => $estado,
            'cuerpo' => $cuerpo,
            'ubicacion' => $respuesta->hasHeader('Location') ? $respuesta->getHeaderLine('Location') : null,
        ];
    }
}
