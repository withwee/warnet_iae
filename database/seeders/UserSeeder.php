<?php

namespace Database\Seeders;  
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'     => 'User',
            'email'    => 'user@example.com',
            'password' => Hash::make('password123'),
            'nik'      => '1234567890123453', // Tambahkan nilai untuk nik
            'no_kk'    => '1234567890123453',
            'phone'    => '08123456789',
            'jumlah_LK' => 2,
            'jumlah_PR' => 3,
            'role'     => 'user',
        ]);
    }
}

