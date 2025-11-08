<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nik' => ['required', 'string', 'size:16', 'unique:'.User::class],
            'no_kk' => ['required', 'string', 'size:16', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:15'],
            'jumlah_LK' => ['required', 'integer', 'min:0'],
            'jumlah_PR' => ['required', 'integer', 'min:0'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'phone' => $request->phone,
            'jumlah_LK' => $request->jumlah_LK,
            'jumlah_PR' => $request->jumlah_PR,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role is user
        ]);

        event(new Registered($user));

        Auth::login($user);

        // User always redirects to user dashboard after registration
        return redirect(route('dashboard', absolute: false))->with('message', 'Registrasi berhasil! Selamat datang.');
    }
}
