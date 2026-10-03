<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Comercio;
use App\Models\Transaccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password']
        );

        $comercios = collect([
            ['nombre_comercio' => 'Café Amanecer', 'rubro' => 'Restaurante', 'fecha_afiliacion' => '2026-09-01', 'telefono' => '2222-1001', 'correo_contacto' => 'cafe@example.test'],
            ['nombre_comercio' => 'Ferretería San José', 'rubro' => 'Comercio', 'fecha_afiliacion' => '2026-09-02', 'telefono' => '2222-1002', 'correo_contacto' => 'ferreteria@example.test'],
            ['nombre_comercio' => 'Pupusería El Buen Sabor', 'rubro' => 'Restaurante', 'fecha_afiliacion' => '2026-09-03', 'telefono' => '2222-1003', 'correo_contacto' => 'pupuseria@example.test'],
        ])->mapWithKeys(function (array $attributes) {
            $comercio = Comercio::updateOrCreate(
                ['nombre_comercio' => $attributes['nombre_comercio']],
                $attributes
            );

            return [$comercio->nombre_comercio => $comercio];
        });

        $transacciones = [
            ['comercio' => 'Café Amanecer', 'cliente_nombre' => 'María López', 'monto' => '25.50', 'metodo_pago' => 'Tarjeta', 'estado' => 'Aprobada'],
            ['comercio' => 'Café Amanecer', 'cliente_nombre' => 'Carlos Pérez', 'monto' => '89.00', 'metodo_pago' => 'Transferencia', 'estado' => 'Procesando'],
            ['comercio' => 'Ferretería San José', 'cliente_nombre' => 'Ana Martínez', 'monto' => '120.00', 'metodo_pago' => 'Tarjeta', 'estado' => 'Aprobada'],
            ['comercio' => 'Ferretería San José', 'cliente_nombre' => 'José Rivera', 'monto' => '18.75', 'metodo_pago' => 'Billetera', 'estado' => 'Rechazada'],
            ['comercio' => 'Pupusería El Buen Sabor', 'cliente_nombre' => 'Lucía Gómez', 'monto' => '12.00', 'metodo_pago' => 'Tarjeta', 'estado' => 'Iniciada'],
        ];

        foreach ($transacciones as $attributes) {
            $comercio = $comercios->get($attributes['comercio']);
            $transaccion = Transaccion::updateOrCreate(
                [
                    'comercio_id' => $comercio->id,
                    'cliente_nombre' => $attributes['cliente_nombre'],
                ],
                [
                    'monto' => $attributes['monto'],
                    'moneda' => 'USD',
                    'metodo_pago' => $attributes['metodo_pago'],
                    'estado' => $attributes['estado'],
                ]
            );

            if ($transaccion->estado !== 'Iniciada') {
                $transaccion->eventos()->firstOrCreate(
                    ['estado_nuevo' => $transaccion->estado],
                    ['estado_anterior' => 'Iniciada']
                );
            }
        }
    }
}
