<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User default
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Cipengs',
            'email' => 'cipengs@example.com',
            'password' => bcrypt('password'),
            'nik' => '1234567890123456',
            'no_kk' => '1234567890123456',
            'phone' => '081234567890',
            'jumlah_LK' => 2,
            'jumlah_PR' => 3,
            'role' => 'user',
        ]);

        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            IuranSeeder::class,
        ]);
    }
}
