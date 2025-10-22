<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_hacer_checkout()
    {
        // Usuario logueado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Producto con stock
        $product = Product::factory()->create(['stock' => 10]);

        // Agregar al carrito (POST /cart  -> route('cart.add'))
        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ])->assertStatus(302);

        // Procesar checkout (POST /checkout -> route('checkout.process'))
        $response = $this->post(route('checkout.process'), [
            'payment_method' => 'card',
            'customer_name'  => 'Test User',        // requeridos por tu validador
            'customer_email' => 'test@example.com', // requeridos por tu validador
            // agrega aquí otros campos si tu validador los pide (teléfono, dirección, etc.)
        ]);

        // Debe redirigir
        $response->assertStatus(302);

        // Debe existir una orden para este usuario
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);

        // Tomamos la última orden creada
        $order = Order::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($order, 'No se encontró la orden recién creada.');

        // Esperamos redirección a /order/success/{order}
        $expected = route('order.success', ['order' => $order->id]); // GET /order/success/{order}
        // Aceptamos tanto absoluta como relativa
        $location = $response->headers->get('Location');
        $this->assertTrue(
            in_array($location, [$expected, parse_url($expected, PHP_URL_PATH)], true),
            'Se esperaba redirección a: '.$expected.' pero fue a: '.$location
        );
    }
}
