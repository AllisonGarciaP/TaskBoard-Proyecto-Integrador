<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Semana7BladeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_the_commerce_index(): void
    {
        $this->get('/')->assertRedirect('/comercios');
    }

    public function test_commerce_index_renders_activity_badges_and_empty_catalog_fallback(): void
    {
        $this->seed();

        $this->get('/comercios')
            ->assertOk()
            ->assertSeeText('Café Amanecer')
            ->assertSeeText('Ferretería El Tornillo')
            ->assertSeeText('Pupusería Doña Marta')
            ->assertSeeText('2 transacciones')
            ->assertSeeText('Sin actividad')
            ->assertSeeText('Activo');
    }

    public function test_commerce_detail_renders_state_badges_and_empty_transaction_fallback(): void
    {
        $this->seed();

        $this->get('/comercios/1')
            ->assertOk()
            ->assertSeeText('María López')
            ->assertSeeText('Completada')
            ->assertSeeText('Juan Pérez')
            ->assertSeeText('Iniciada');

        $this->get('/comercios/2')
            ->assertOk()
            ->assertSeeText('Sin transacciones');
    }

    public function test_commerce_route_model_binding_returns_404_for_unknown_records(): void
    {
        $this->get('/comercios/999999')->assertNotFound();
    }
}