<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $storeIds = Store::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        // Genera todas las combinaciones posibles
        $combinations = [];
        foreach ($storeIds as $storeId) {
            foreach ($productIds as $productId) {
                $combinations[] = ['store' => $storeId, 'product' => $productId];
            }
        }

        // Mezcla las combinaciones y toma las primeras 5
        shuffle($combinations);
        $selected = array_slice($combinations, 0, 5);

        foreach ($selected as $combo) {
            Stock::create([
                'store' => $combo['store'],
                'product' => $combo['product'],
                'quantity' => $faker->numberBetween(1, 50)
            ]);
        }
    }
}
