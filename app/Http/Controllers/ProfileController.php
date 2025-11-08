<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user() ?? auth()->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'nik' => ['required', 'string', 'size:16', 'unique:users,nik,' . $user->id],
            'no_kk' => ['required', 'string', 'size:16'],
            'phone' => ['required', 'string', 'max:15'],
            'jumlah_LK' => ['required', 'integer', 'min:0'],
            'jumlah_PR' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'], // Max 1MB
        ], [
            'photo.max' => 'Ukuran foto maksimal 1MB. Silakan kompres foto Anda terlebih dahulu.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus jpeg, png, jpg, atau gif.',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                \Storage::disk('public')->delete($user->photo);
            }
            
            $photoPath = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Profile berhasil diperbarui!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
