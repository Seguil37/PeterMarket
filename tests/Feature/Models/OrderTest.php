<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_pedido_pertenece_a_un_usuario_y_tiene_items(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->for($user)->create();

        $p1 = Product::factory()->create(['price' => 10.50]);
        $p2 = Product::factory()->create(['price' => 5.25]);

        $i1 = OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $p1->id,
            'name'       => $p1->name,
            'price'      => $p1->price,
            'quantity'   => 2,
            'line_total' => 21.00,
        ]);

        $i2 = OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $p2->id,
            'name'       => $p2->name,
            'price'      => $p2->price,
            'quantity'   => 3,
            'line_total' => 15.75,
        ]);

        $order->refresh();

        $this->assertEquals($user->id, $order->user->id);
        $this->assertCount(2, $order->items);
        $this->assertEquals($order->id, $i1->order->id);
        $this->assertEquals($p2->id, $i2->product->id);
    }

    public function test_calcula_line_total_como_price_por_quantity(): void
    {
        $order = Order::factory()->create();
        $p     = Product::factory()->create(['price' => 7.40]);

        $item = OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $p->id,
            'name'       => $p->name,
            'price'      => 7.40,
            'quantity'   => 5,
            'line_total' => 37.00, // 7.40 * 5
        ]);

        $this->assertEquals(37.00, (float) $item->line_total);
    }

    public function test_puede_acumular_subtotal_impuesto_y_total(): void
    {
        $order = Order::factory()->create();

        // 12.50 * 2 = 25.00 y 8.00 * 1 = 8.00 => subtotal 33.00
        OrderItem::factory()->create([
            'order_id'   => $order->id,
            'price'      => 12.50,
            'quantity'   => 2,
            'line_total' => 25.00,
        ]);

        OrderItem::factory()->create([
            'order_id'   => $order->id,
            'price'      => 8.00,
            'quantity'   => 1,
            'line_total' => 8.00,
        ]);

        $subtotal = $order->items()->sum('line_total'); // 33.00
        $taxRate  = 0.12; // 12% de impuesto
        $tax      = round($subtotal * $taxRate, 2);
        $total    = round($subtotal + $tax, 2);

        $order->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $total,
        ]);

        $order->refresh();

        $this->assertEquals(33.00, (float) $order->subtotal);
        $this->assertEquals(3.96, (float) $order->tax);   // 33 * 0.12
        $this->assertEquals(36.96, (float) $order->total);
    }
}
