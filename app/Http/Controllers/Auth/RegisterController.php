<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'nik' => ['required', 'string', 'size:16', 'unique:users'],
            'no_kk' => ['required', 'string', 'size:16'],
            'jumlah_laki' => ['required', 'integer', 'min:0'],
            'jumlah_perempuan' => ['required', 'integer', 'min:0'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'jumlah_laki' => $request->jumlah_laki,
            'jumlah_perempuan' => $request->jumlah_perempuan,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login.');
    }
}