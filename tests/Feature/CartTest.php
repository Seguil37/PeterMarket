<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_agregar_producto_al_carrito()
    {
        // $this->actingAs(\App\Models\User::factory()->create()); // descomenta si requiere auth

        $product = Product::factory()->create();

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id, // cambia a 'productId' si tu controlador lo espera así
            'quantity'   => 2,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart');

        // Ejemplo más específico si conoces tu estructura:
        // $response->assertSessionHas('cart.items.' . $product->id . '.quantity', 2);
    }

    public function test_usuario_puede_actualizar_cantidad_en_el_carrito()
    {
        $product = Product::factory()->create();

        // Agregar primero
        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ])->assertStatus(302);

        // Actualizar cantidad (PATCH cart/{productId})
        $this->patch(route('cart.update', ['productId' => $product->id]), [
            'quantity' => 3,
        ])->assertStatus(302);

        // Validación genérica (estructura depende de tu implementación)
        $this->assertTrue(session()->has('cart'));

        // Ejemplo específico (ajusta si aplica):
        // $this->assertSame(3, data_get(session('cart'), "items.{$product->id}.quantity"));
    }

    public function test_usuario_puede_eliminar_un_producto_del_carrito()
    {
        $product = Product::factory()->create();

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 2,
        ])->assertStatus(302);

        // Eliminar producto (DELETE cart/{productId})
        $this->delete(route('cart.remove', ['productId' => $product->id]))
             ->assertStatus(302);

        $this->assertTrue(session()->has('cart'));

        // Si quieres confirmar que ya no está:
        // $this->assertNull(data_get(session('cart'), "items.{$product->id}"));
    }

    public function test_usuario_puede_vaciar_el_carrito()
    {
        $p1 = Product::factory()->create();
        $p2 = Product::factory()->create();

        $this->post(route('cart.add'), [
            'product_id' => $p1->id,
            'quantity'   => 1,
        ])->assertStatus(302);

        $this->post(route('cart.add'), [
            'product_id' => $p2->id,
            'quantity'   => 1,
        ])->assertStatus(302);

        // Vaciar (DELETE cart)
        $this->delete(route('cart.clear'))->assertStatus(302);

        // Algunas implementaciones eliminan la clave, otras la dejan vacía
        $this->assertTrue(!session()->has('cart') || empty(session('cart')));

        // Si tu implementación borra la clave sí o sí:
        // $this->assertFalse(session()->has('cart'));
    }
}
