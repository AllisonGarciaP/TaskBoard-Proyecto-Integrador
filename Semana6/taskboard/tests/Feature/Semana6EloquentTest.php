<?php

namespace Tests\Feature;

use App\Models\Comercio;
use App\Models\Transaccion;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Semana6EloquentTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrations_create_domain_tables_columns_and_foreign_keys(): void
    {
        $this->assertTrue(Schema::hasColumns('comercios', [
            'id', 'nombre_comercio', 'rubro', 'fecha_afiliacion', 'telefono', 'correo_contacto', 'created_at', 'updated_at',
        ]));
        $this->assertTrue(Schema::hasColumns('transacciones', [
            'id', 'comercio_id', 'monto', 'moneda', 'cliente_nombre', 'metodo_pago', 'estado', 'created_at', 'updated_at',
        ]));
        $this->assertTrue(Schema::hasColumns('eventos_transaccion', [
            'id', 'transaccion_id', 'estado_anterior', 'estado_nuevo', 'created_at', 'updated_at',
        ]));

        $transaccionKeys = Schema::getForeignKeys('transacciones');
        $eventoKeys = Schema::getForeignKeys('eventos_transaccion');

        $this->assertSame('comercios', $transaccionKeys[0]['foreign_table']);
        $this->assertSame('transacciones', $eventoKeys[0]['foreign_table']);
    }

    public function test_eloquent_relationships_create_and_navigate_related_records(): void
    {
        $comercio = $this->createComercio();
        $transaccion = $comercio->transacciones()->create([
            'monto' => '45.00',
            'moneda' => 'USD',
            'cliente_nombre' => 'María López',
            'metodo_pago' => 'Tarjeta',
            'estado' => 'Procesando',
        ]);
        $evento = $transaccion->eventos()->create([
            'estado_anterior' => 'Iniciada',
            'estado_nuevo' => 'Procesando',
        ]);

        $this->assertSame($comercio->id, $transaccion->comercio->id);
        $this->assertSame($transaccion->id, $evento->transaccion->id);
        $this->assertCount(1, $comercio->transacciones);
        $this->assertCount(1, $transaccion->eventos);
    }

    public function test_real_data_controllers_return_nested_records_and_binding_404s(): void
    {
        $comercio = $this->createComercio();
        $transaccion = $comercio->transacciones()->create([
            'monto' => '25.50',
            'moneda' => 'USD',
            'cliente_nombre' => 'Carlos Pérez',
            'metodo_pago' => 'Tarjeta',
            'estado' => 'Aprobada',
        ]);
        $transaccion->eventos()->create([
            'estado_anterior' => 'Procesando',
            'estado_nuevo' => 'Aprobada',
        ]);

        $this->get('/comercios')
            ->assertOk()
            ->assertJsonPath('0.nombre_comercio', $comercio->nombre_comercio)
            ->assertJsonPath('0.transacciones.0.monto', '25.50');
        $this->get('/comercio/' . $comercio->id)
            ->assertOk()
            ->assertJsonPath('id', $comercio->id);
        $this->get('/comercio/999999')->assertNotFound();
        $this->get('/comercios/abc')->assertNotFound();

        $this->get('/transacciones')
            ->assertOk()
            ->assertJsonPath('0.comercio.nombre_comercio', $comercio->nombre_comercio);
        $this->get('/transaccion/' . $transaccion->id)
            ->assertOk()
            ->assertJsonPath('comercio.nombre_comercio', $comercio->nombre_comercio);
        $this->get('/transaccion/999999')->assertNotFound();

        $this->get('/eventos-transaccion')
            ->assertOk()
            ->assertJsonPath('0.transaccion.comercio.nombre_comercio', $comercio->nombre_comercio);
    }

    public function test_commerce_name_must_be_unique(): void
    {
        $this->createComercio();

        $this->expectException(QueryException::class);
        $this->createComercio();
    }

    public function test_eager_loading_reduces_commerce_queries_from_six_to_two(): void
    {
        $comercio = $this->createComercio();

        foreach (range(1, 5) as $number) {
            $comercio->transacciones()->create([
                'monto' => '10.00',
                'moneda' => 'USD',
                'cliente_nombre' => "Cliente $number",
                'metodo_pago' => 'Tarjeta',
                'estado' => 'Aprobada',
            ]);
        }

        $connection = DB::connection();
        $connection->enableQueryLog();
        $connection->flushQueryLog();
        $transacciones = Transaccion::all();

        foreach ($transacciones as $transaccion) {
            $transaccion->comercio->nombre_comercio;
        }

        $queriesWithoutEagerLoading = count($connection->getQueryLog());
        $connection->flushQueryLog();
        $transacciones = Transaccion::with('comercio')->get();

        foreach ($transacciones as $transaccion) {
            $transaccion->comercio->nombre_comercio;
        }

        $queriesWithEagerLoading = count($connection->getQueryLog());
        $connection->disableQueryLog();

        $this->assertSame(6, $queriesWithoutEagerLoading);
        $this->assertSame(2, $queriesWithEagerLoading);
    }

    public function test_database_seeder_is_repeatable_and_creates_all_examples(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('comercios', 3);
        $this->assertDatabaseCount('transacciones', 5);
        $this->assertDatabaseCount('eventos_transaccion', 4);
    }

    private function createComercio(string $nombre = 'Café Amanecer'): Comercio
    {
        return Comercio::create([
            'nombre_comercio' => $nombre,
            'rubro' => 'Restaurante',
            'fecha_afiliacion' => '2026-09-01',
        ]);
    }
}