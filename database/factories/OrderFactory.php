<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'customer_name'     => $this->faker->name(),
            'customer_email'    => $this->faker->safeEmail(),
            'customer_address'  => $this->faker->address(),
            'payment_method'    => $this->faker->randomElement(['card','cash','transfer']),
            'status'            => $this->faker->randomElement(['pending','paid','canceled']),
            'subtotal'          => 0,
            'tax'               => 0,
            'total'             => 0,
            'payment_ref'       => $this->faker->uuid(),
        ];
    }
}
