<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $avialable = $this->faker->numberBetween(0, 100);
        $reserved = $this->faker->numberBetween(0, 30);
        $total = $avialable + $reserved;
        $sold = $this->faker->numberBetween(0, 50);

        return [
            'store' => Store::factory(),
            'product' => Product::factory(),
            'available_quantity' => $avialable,
            'reserved_quantity' => $reserved,
            'total_quantity' => $total,
            'sold_quantity' => $sold
        ];
    }
}
