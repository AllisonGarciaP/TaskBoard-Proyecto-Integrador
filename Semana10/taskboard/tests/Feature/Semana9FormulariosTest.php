<?php

namespace Tests\Feature;

use App\Models\Comercio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Semana9FormulariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_sandbox_contains_html_controls_and_a_csrf_protected_post_form(): void
    {
        $this->get('/practica/formulario-demo')
            ->assertOk()
            ->assertSee('method="POST"', false)
            ->assertSee('name="cliente_nombre"', false)
            ->assertSee('name="correo_contacto"', false)
            ->assertSee('name="monto"', false)
            ->assertSee('name="estado"', false)
            ->assertSee('name="recurrente"', false)
            ->assertSee('name="_token"', false);

        $this->post('/practica/enviar')->assertOk()->assertSeeText('Formulario recibido correctamente.');
    }

    public function test_commerce_search_filters_by_name_and_rubro_and_preserves_query_values(): void
    {
        $this->seed();

        $this->get('/comercios?buscar=Amanecer')
            ->assertOk()
            ->assertSeeText('Café Amanecer')
            ->assertDontSeeText('Ferretería El Tornillo')
            ->assertSee('value="Amanecer"', false);

        $this->get('/comercios?rubro=Ferretería')
            ->assertOk()
            ->assertSeeText('Ferretería El Tornillo')
            ->assertDontSeeText('Café Amanecer');

        $this->get('/comercios?buscar=Amanecer&rubro=Restaurante')
            ->assertOk()
            ->assertSeeText('Café Amanecer')
            ->assertDontSeeText('Pupusería Doña Marta');

        $this->get('/comercios?buscar=no-existe')
            ->assertOk()
            ->assertSeeText('Aún no hay comercios afiliados.');
    }

    public function test_transaction_form_creates_only_allowed_fields_and_uses_prg_flash(): void
    {
        $comercio = Comercio::create([
            'nombre_comercio' => 'Café Amanecer',
            'rubro' => 'Restaurante',
            'telefono' => '2222-1234',
        ]);

        $this->get(route('transacciones.create', $comercio))
            ->assertOk()
            ->assertSeeText('Nueva transacción')
            ->assertSee('name="_token"', false)
            ->assertSee('name="comercio_id" value="' . $comercio->id . '"', false);

        $response = $this->post(route('transacciones.store'), [
            'comercio_id' => $comercio->id,
            'cliente_nombre' => 'María López',
            'monto' => '45.00',
            'estado' => 'Completada',
        ]);

        $response->assertRedirect(route('comercios.show', $comercio))
            ->assertSessionHas('mensaje', 'Transacción registrada con éxito.');

        $this->assertDatabaseHas('transacciones', [
            'comercio_id' => $comercio->id,
            'cliente_nombre' => 'María López',
            'monto' => '45.00',
            'estado' => 'Iniciada',
        ]);
        $this->assertDatabaseCount('transacciones', 1);

        $this->get(route('comercios.show', $comercio))
            ->assertOk()
            ->assertSeeText('Transacción registrada con éxito.')
            ->assertSeeText('María López')
            ->assertSeeText('Iniciada');

        $this->get(route('comercios.show', $comercio))
            ->assertOk()
            ->assertDontSeeText('Transacción registrada con éxito.');
        $this->assertDatabaseCount('transacciones', 1);
    }
}