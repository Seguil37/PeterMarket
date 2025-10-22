<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_exitoso_redirige_al_dashboard()
    {
        $user = User::factory()->create([
            'email' => 'demo@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [ // usa la URL directa
            'email' => 'demo@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }
}
