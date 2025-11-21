<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_crea_orden_y_descuenta_stock(): void
    {
        $firstProduct = Product::factory()->create([
            'price' => 25.00,
            'stock' => 12,
            'category_type' => 'Abarrotes',
        ]);

        $secondProduct = Product::factory()->create([
            'price' => 20.50,
            'stock' => 8,
            'category_type' => 'Bebidas',
        ]);

        $cart = [
            $firstProduct->id => [
                'id' => $firstProduct->id,
                'name' => $firstProduct->name,
                'price' => (float) $firstProduct->price,
                'image' => $firstProduct->image_url,
                'quantity' => 2,
                'stock' => $firstProduct->stock,
                'category_type' => $firstProduct->category_type,
            ],
            $secondProduct->id => [
                'id' => $secondProduct->id,
                'name' => $secondProduct->name,
                'price' => (float) $secondProduct->price,
                'image' => $secondProduct->image_url,
                'quantity' => 1,
                'stock' => $secondProduct->stock,
                'category_type' => $secondProduct->category_type,
            ],
        ];

        $payload = [
            'customer_name' => 'Cliente Test',
            'customer_email' => 'cliente@example.com',
            'shipping_address' => 'Calle Falsa 123',
            'shipping_city' => 'Lima',
            'shipping_reference' => 'Puerta negra',
            'shipping_type' => 'standard',
            'payment_method' => 'simulated',
        ];

        $response = $this->withSession(['cart' => $cart])
            ->post(route('checkout.process'), $payload);

        $order = Order::with('items')->first();

        $response->assertRedirect(route('order.success', $order));
        $this->assertNotNull($order, 'Se esperaba que se generara una orden.');
        $this->assertCount(2, $order->items);

        $expectedSubtotal = round((25.00 * 2) + 20.50, 2);
        $expectedTax = round($expectedSubtotal * 0.18, 2);
        $expectedTotal = round($expectedSubtotal + $expectedTax, 2);

        $this->assertSame($expectedSubtotal, (float) $order->subtotal);
        $this->assertSame($expectedTax, (float) $order->tax);
        $this->assertSame($expectedTotal, (float) $order->total);

        $this->assertSame(10, $firstProduct->fresh()->stock);
        $this->assertSame(7, $secondProduct->fresh()->stock);

        $this->assertFalse(session()->has('cart'));
        $this->assertEqualsCanonicalizing([
            $firstProduct->id,
            $secondProduct->id,
        ], OrderItem::pluck('product_id')->all());
    }
}
