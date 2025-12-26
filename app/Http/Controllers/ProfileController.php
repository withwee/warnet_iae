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

    // API Methods
    public function showApi(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateApi(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'nik' => ['sometimes', 'required', 'string', 'size:16', 'unique:users,nik,' . $user->id],
            'no_kk' => ['sometimes', 'required', 'string', 'size:16'],
            'phone' => ['sometimes', 'required', 'string', 'max:15'],
            'jumlah_LK' => ['sometimes', 'required', 'integer', 'min:0'],
            'jumlah_PR' => ['sometimes', 'required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo);
            }
            $photoPath = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json($user);
    }

    public function destroyApi(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Incorrect password.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Account deleted successfully.'], 200);
    }
}
