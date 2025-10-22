<?php

namespace Database\Factories;

use App\Models\Compra;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompraFactory extends Factory
{
    protected $model = Compra::class;

    public function definition(): array
    {
        $qty   = $this->faker->numberBetween(1, 10);
        $price = $this->faker->randomFloat(2, 1, 200);

        return [
            'producto' => $this->faker->words(3, true),
            'cantidad' => $qty,
            'precio'   => $price,
            'total'    => round($qty * $price, 2),
        ];
    }
}
