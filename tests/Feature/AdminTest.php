<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_crear_un_producto()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->post('/admin/products', [
            'name'  => 'Aceite',
            'price' => 10.5,
            'stock' => 20,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', ['name' => 'Aceite']);
    }
}
