<?php

namespace App\Observatorio;

/**
 * Generador pseudoaleatorio determinista (Lehmer / "minimal standard").
 * No depende de la versión de PHP: la misma semilla produce siempre los mismos datos.
 * La semilla se mezcla con una función hash para que semillas consecutivas den datos muy distintos.
 *
 * NO MODIFICAR (parte de la infraestructura del parcial).
 */
final class Prng
{
    private const MODULO = 2147483647;

    private int $x;

    public function __construct(int $semilla)
    {
        $x = $semilla & 0x7fffffff;
        for ($i = 0; $i < 3; $i++) {
            $x = ((($x >> 16) ^ $x) * 0x45d9f3b) & 0x7fffffff;
        }
        $x = $x % (self::MODULO - 1) + 1;
        $this->x = $x;
        for ($i = 0; $i < 4; $i++) {
            $this->siguiente();
        }
    }

    public function siguiente(): int
    {
        $this->x = ($this->x * 48271) % self::MODULO;

        return $this->x;
    }

    /** Entero entre $min y $max (ambos incluidos). */
    public function entre(int $min, int $max): int
    {
        return $min + (($this->siguiente() >> 4) % ($max - $min + 1));
    }
}
