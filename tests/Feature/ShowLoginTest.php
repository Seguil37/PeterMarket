<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function muestra_vista_si_es_invitado_y_redirige_si_autenticado()
    {
        // Invitado ve la vista
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertViewIs('auth.login');

        // Autenticado redirige al dashboard
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
