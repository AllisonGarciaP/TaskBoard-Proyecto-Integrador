<?php

namespace Database\Seeders;

use App\Models\Comercio;
use Illuminate\Database\Seeder;

class ComercioSeeder extends Seeder
{
    /**
     * Datos de ejemplo usados en todas las guías de Semana 6-8:
     * - Café Amanecer: comercio CON transacciones (uno Completada, uno Iniciada).
     * - Ferretería El Tornillo: comercio SIN transacciones (para probar @empty).
     */
    public function run(): void
    {
        $cafeAmanecer = Comercio::updateOrCreate(
            ['nombre_comercio' => 'Café Amanecer'],
            ['rubro' => 'Restaurante', 'telefono' => '2222-1234']
        );

        foreach ([
            [
                'monto' => 45.00,
                'cliente_nombre' => 'María López',
                'estado' => 'Completada',
            ],
            [
                'monto' => 12.50,
                'cliente_nombre' => 'Juan Pérez',
                'estado' => 'Iniciada',
            ],
        ] as $attributes) {
            $cafeAmanecer->transacciones()->updateOrCreate(
                ['cliente_nombre' => $attributes['cliente_nombre']],
                $attributes
            );
        }

        Comercio::updateOrCreate(
            ['nombre_comercio' => 'Ferretería El Tornillo'],
            ['rubro' => 'Ferretería', 'telefono' => '2222-5678']
        );

        $pupuseria = Comercio::updateOrCreate(
            ['nombre_comercio' => 'Pupusería Doña Marta'],
            ['rubro' => 'Restaurante', 'telefono' => '2222-9012']
        );

        $pupuseria->transacciones()->updateOrCreate(
            ['cliente_nombre' => 'Carlos Ramírez'],
            ['monto' => 8.00, 'estado' => 'Fallida']
        );
    }
}
