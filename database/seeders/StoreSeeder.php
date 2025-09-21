<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create([
            'name' => 'Store A',
            'user' => User::where('email', 'admin@storea.com')->first()->id
        ]);

        Store::create([
            'name' => 'Store B',
            'user' => User::where('email', 'admin@storeb.com')->first()->id
        ]);

        Store::create([
            'name' => 'Store C',
            'user' => User::where('email', 'admin@storec.com')->first()->id
        ]);
    }
}
