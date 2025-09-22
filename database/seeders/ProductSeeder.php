<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        Product::create([
            'name' => 'Product A',
            'ean13' => $faker->ean13(),
            'description' => 'Description for Product A'
        ]);

        Product::create([
            'name' => 'Product B',
            'ean13' => $faker->ean13(),
            'description' => 'Description for Product B'
        ]);

        Product::create([
            'name' => 'Product C',
            'ean13' => $faker->ean13(),
            'description' => 'Description for Product C'
        ]);
    }
}
