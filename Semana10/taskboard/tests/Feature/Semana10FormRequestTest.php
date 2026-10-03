<?php

namespace Tests\Feature;

use App\Http\Requests\GuardarTransaccionRequest;
use App\Models\Comercio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Semana10FormRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_request_authorizes_and_contains_week_ten_rules_and_messages(): void
    {
        $request = new GuardarTransaccionRequest();

        $this->assertTrue($request->authorize());
        $this->assertSame([
            'comercio_id' => 'required|exists:comercios,id',
            'cliente_nombre' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
        ], $request->rules());
        $this->assertSame('El monto debe ser un número.', $request->messages()['monto.numeric']);
        $this->assertSame('monto de la transacción', $request->attributes()['monto']);
    }

    public function test_invalid_fields_show_custom_errors_and_keep_old_input(): void
    {
        $comercio = $this->createComercio();
        $formUrl = route('transacciones.create', $comercio);

        $this->from($formUrl)
            ->post(route('transacciones.store'), [
                'comercio_id' => $comercio->id,
                'cliente_nombre' => 'Cliente conservado',
                'monto' => 'abc',
            ])
            ->assertRedirect($formUrl)
            ->assertSessionHasErrors([
                'monto' => 'El monto debe ser un número.',
            ]);

        $this->get($formUrl)
            ->assertOk()
            ->assertSee('value="Cliente conservado"', false)
            ->assertSee('El monto debe ser un número.');

        $this->from($formUrl)
            ->post(route('transacciones.store'), [
                'comercio_id' => $comercio->id,
                'cliente_nombre' => '',
                'monto' => '',
            ])
            ->assertSessionHasErrors([
                'cliente_nombre' => 'Debes indicar el nombre del cliente.',
                'monto' => 'Debes indicar un monto.',
            ]);

        $this->from($formUrl)
            ->post(route('transacciones.store'), [
                'comercio_id' => $comercio->id,
                'cliente_nombre' => 'Cliente válido',
                'monto' => '-5',
            ])
            ->assertSessionHasErrors([
                'monto' => 'El monto debe ser mayor a cero.',
            ]);

        $this->assertDatabaseCount('transacciones', 0);
    }

    public function test_nonexistent_commerce_is_rejected_by_exists_rule(): void
    {
        $this->post(route('transacciones.store'), [
            'comercio_id' => 9999,
            'cliente_nombre' => 'Cliente válido',
            'monto' => '10.00',
        ])->assertSessionHasErrors([
            'comercio_id' => 'El comercio seleccionado no existe.',
        ]);

        $this->assertDatabaseCount('transacciones', 0);
    }

    public function test_form_request_keeps_prg_behavior_and_ignores_unrequested_state(): void
    {
        $comercio = $this->createComercio();

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
    }

    private function createComercio(): Comercio
    {
        return Comercio::create([
            'nombre_comercio' => 'Café Amanecer',
            'rubro' => 'Restaurante',
            'telefono' => '2222-1234',
        ]);
    }
}