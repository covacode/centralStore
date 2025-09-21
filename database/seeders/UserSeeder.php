<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'User Store A',
            'email' => 'admin@storea.com',
            'password' => Hash::make('12345678')
        ]);

        User::factory()->create([
            'name' => 'User Store B',
            'email' => 'admin@storeb.com',
            'password' => Hash::make('12345678')
        ]);

        User::factory()->create([
            'name' => 'User Store C',
            'email' => 'admin@storec.com',
            'password' => Hash::make('12345678')
        ]);
    }
}
