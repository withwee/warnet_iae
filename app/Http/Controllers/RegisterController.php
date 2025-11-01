<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nik' => 'required|digits_between:10,20|unique:users',
            'kk' => 'required|digits_between:10,20',
            'jumlah_laki' => 'required|integer|min:0',
            'jumlah_perempuan' => 'required|integer|min:0',
            'no_hp' => 'required|digits_between:10,15',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['nama_lengkap'], // disimpan sebagai name
            'email' => $validated['email'],
            'nik' => $validated['nik'],
            'kk' => $validated['kk'],
            'laki_laki' => $validated['jumlah_laki'],
            'perempuan' => $validated['jumlah_perempuan'],
            'jumlah_keluarga' => $validated['jumlah_laki'] + $validated['jumlah_perempuan'],
            'phone' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('/')->with('success', 'Pendaftaran berhasil!');
    }
}
