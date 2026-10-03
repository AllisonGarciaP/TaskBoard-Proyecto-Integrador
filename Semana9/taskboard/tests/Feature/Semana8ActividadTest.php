<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Semana8ActividadTest extends TestCase
{
    use RefreshDatabase;

    public function test_commerce_detail_shows_the_correct_activity_summary_for_zero_one_and_many_transactions(): void
    {
        $this->seed();

        $this->get('/comercios/2')
            ->assertOk()
            ->assertSeeText('Este comercio es nuevo, aún no registra actividad.');

        $this->get('/comercios/3')
            ->assertOk()
            ->assertSeeText('Este comercio tiene su primera transacción registrada.');

        $this->get('/comercios/1')
            ->assertOk()
            ->assertSeeText('Este comercio tiene un historial de 2 transacciones.');
    }
}