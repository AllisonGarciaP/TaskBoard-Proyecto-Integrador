<?php

namespace Tests\Feature;

use App\Models\Reserva;
use App\Models\Telescopio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_issues_a_personal_token_and_rejects_bad_credentials(): void
    {
        User::factory()->create([
            'email' => 'ana@taskboard.test',
            'password' => 'password123',
        ]);

        $this->postJson('/api/login', [
            'email' => 'ana@taskboard.test',
            'password' => 'password123',
            'dispositivo' => 'prueba-automatizada',
        ])
            ->assertCreated()
            ->assertJsonPath('tipo', 'Bearer')
            ->assertJsonPath('usuario.email', 'ana@taskboard.test')
            ->assertJsonStructure(['token', 'tipo', 'usuario' => ['id', 'name', 'email']]);

        $this->postJson('/api/login', [
            'email' => 'ana@taskboard.test',
            'password' => 'incorrecta',
            'dispositivo' => 'prueba-automatizada',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'Las credenciales no coinciden.');
    }

    public function test_api_routes_require_authentication(): void
    {
        $this->getJson('/api/yo')->assertUnauthorized();
        $this->getJson('/api/telescopios')->assertUnauthorized();
        $this->getJson('/api/reservas')->assertUnauthorized();
        $this->getJson('/api/tokens')->assertUnauthorized();
    }

    public function test_reservations_belong_to_the_authenticated_user(): void
    {
        $ana = User::factory()->create();
        $carlos = User::factory()->create();
        $telescopio = Telescopio::create([
            'nombre' => 'Telescopio de prueba',
            'tipo' => 'Reflector',
            'apertura_mm' => 150,
            'activo' => true,
        ]);

        $this->actingAs($ana, 'sanctum')
            ->postJson('/api/reservas', [
                'user_id' => $carlos->id,
                'telescopio_id' => $telescopio->id,
                'fecha' => '2027-01-01',
                'horas' => 2,
                'objetivo' => 'Prueba de propiedad',
            ])
            ->assertCreated()
            ->assertJsonPath('data.user_id', $ana->id);

        $reserva = Reserva::query()->firstOrFail();
        $this->assertSame($ana->id, $reserva->user_id);

        $this->actingAs($carlos, 'sanctum')
            ->getJson('/api/reservas/'.$reserva->id)
            ->assertNotFound();

        $this->actingAs($carlos, 'sanctum')
            ->deleteJson('/api/reservas/'.$reserva->id)
            ->assertNotFound();
    }

    public function test_token_list_hides_hash_and_deleting_a_token_revokes_it(): void
    {
        User::factory()->create([
            'email' => 'ana@taskboard.test',
            'password' => 'password123',
        ]);
        $carlos = User::factory()->create();
        $carlosToken = $carlos->createToken('tablet-carlos')->accessToken;

        $login = $this->postJson('/api/login', [
            'email' => 'ana@taskboard.test',
            'password' => 'password123',
            'dispositivo' => 'laptop-ana',
        ])->assertCreated();

        $plainTextToken = $login->json('token');
        $tokenId = (int) explode('|', $plainTextToken, 2)[0];

        $this->withToken($plainTextToken)
            ->getJson('/api/tokens')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'laptop-ana')
            ->assertJsonMissingPath('0.token');

        $this->withToken($plainTextToken)
            ->deleteJson('/api/tokens/'.$carlosToken->id)
            ->assertNotFound();

        $this->withToken($plainTextToken)
            ->deleteJson('/api/tokens/'.$tokenId)
            ->assertNoContent();

        $this->app['auth']->forgetGuards();

        $this->withToken($plainTextToken)
            ->getJson('/api/yo')
            ->assertUnauthorized();

        $secondLogin = $this->postJson('/api/login', [
            'email' => 'ana@taskboard.test',
            'password' => 'password123',
            'dispositivo' => 'movil-ana',
        ])->assertCreated();

        $secondToken = $secondLogin->json('token');

        $this->withToken($secondToken)
            ->postJson('/api/logout')
            ->assertNoContent();

        $this->app['auth']->forgetGuards();

        $this->withToken($secondToken)
            ->getJson('/api/yo')
            ->assertUnauthorized();
    }

    public function test_sanctum_token_expiration_is_24_hours(): void
    {
        $this->assertSame(60 * 24, config('sanctum.expiration'));
    }
}
