<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user dengan no_kk yang sesuai
        $user = \App\Models\User::where('no_kk', '1234567890123456')->first();

        if ($user) {
            DB::table('iurans')->insert([
                [
                    'user_id' => $user->id,
                    'jenis_iuran' => 'Iuran Sampah',
                    'tgl_bayar' => null,
                    'total_bayar' => 125000,
                    'status' => 'Belum Bayar',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'user_id' => $user->id,
                    'jenis_iuran' => 'Iuran Keamanan',
                    'tgl_bayar' => Carbon::now(),
                    'total_bayar' => 25000,
                    'status' => 'Sudah Bayar',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'user_id' => $user->id,
                    'jenis_iuran' => 'Iuran Acara 17 Agustus',
                    'tgl_bayar' => Carbon::now(),
                    'total_bayar' => 55000,
                    'status' => 'Sudah Bayar',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
            ]);
        }
    }
}
