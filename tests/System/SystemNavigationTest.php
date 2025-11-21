<?php

namespace Tests\System;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemNavigationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Recorre el sistema completo sin depender de Selenium.
     */
    public function test_usuario_puede_recorrer_catalogo_carrito_y_nosotros(): void
    {
        $product = Product::factory()->create([
            'stock' => 5,
            'category_type' => 'Abarrotes',
        ]);

        $catalog = $this->get('/');
        $catalog->assertOk();
        $catalog->assertSee('Productos');
        $catalog->assertSee($product->name);

        $this->post('/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertRedirect(route('cart.index'));

        $cart = $this->get('/cart');
        $cart->assertOk();
        $cart->assertSee('Carrito de compras');
        $cart->assertSee($product->name);
        $cart->assertSee('S/');

        $about = $this->get('/nosotros');
        $about->assertOk();
        $about->assertSee('Nosotros');
        $about->assertSee('soporte@petermarket.local');
    }
}