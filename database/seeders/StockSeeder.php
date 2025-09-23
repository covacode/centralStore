<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stock::create([
            'store' => 1,
            'product' => 1,
            'available_quantity' => 20,
            'reserved_quantity' => 10,
            'total_quantity' => 30,
            'sold_quantity' => 5
        ]);

        Stock::create([
            'store' => 1,
            'product' => 2,
            'available_quantity' => 32,
            'reserved_quantity' => 8,
            'total_quantity' => 40,
            'sold_quantity' => 12
        ]);

        Stock::create([
            'store' => 1,
            'product' => 3,
            'available_quantity' => 15,
            'reserved_quantity' => 12,
            'total_quantity' => 27,
            'sold_quantity' => 8
        ]);
    }
}
