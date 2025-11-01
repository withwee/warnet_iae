<?php

namespace Database\Seeders;  
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        if (!User::where('email', 'admin@example.com')->exists()) {
        User::create([
            'name'     => 'SuperAdmin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'nik'      => '1234567890123458',
            'no_kk'    => '1234567890123458',
            'phone'    => '08123456789',
            'jumlah_LK' => 1,
            'jumlah_PR' => 1,
            'role'     => 'admin',
        ]);
    }}
}

