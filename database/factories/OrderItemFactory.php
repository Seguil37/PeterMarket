<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        $price   = $product->price;
        $qty     = $this->faker->numberBetween(1, 5);

        return [
            'order_id'   => Order::factory(),
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => $price,
            'quantity'   => $qty,
            'line_total' => round($price * $qty, 2),
        ];
    }
}
