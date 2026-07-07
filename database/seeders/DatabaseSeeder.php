<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@supplychain.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'user@supplychain.test',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->call([
            CountrySeeder::class,
            PortSeeder::class,
        ]);
    }
}