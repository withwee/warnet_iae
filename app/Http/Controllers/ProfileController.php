<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class ProfileController extends Controller
{
    public function showEditForm()
    {
        $user = $this->getAuthenticatedUserOrRedirect();
        if (!$user instanceof User) return $user;

        return view('edit-profile', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $authUser = $this->getAuthenticatedUserOrRedirect();
        if (!$authUser instanceof User) return $authUser;

        if ((int) $authUser->id !== (int) $id) {
            return redirect()->back()->withErrors(['error' => 'Tidak diizinkan mengubah data user lain']);
        }

        $validated = $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email',
            'nik'        => 'required|digits:16',
            'no_kk'      => 'required|digits:16',
            'phone'      => 'required|numeric',
            'photo'      => 'nullable|image|max:2048',
            'jumlah_LK'  => 'required|numeric',
            'jumlah_PR'  => 'required|numeric',
        ]);

        // handle foto
        if ($request->hasFile('photo')) {
            if ($authUser->photo) {
                Storage::disk('public')->delete($authUser->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
        }

        $authUser->update($validated);

        // Update session agar tampilan langsung berubah
        session(['user' => [
            'id'    => $authUser->id,
            'name'  => $authUser->name,
            'role'  => $authUser->role,
            'photo' => $authUser->photo,
        ]]);

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    public function deletePhoto($id)
    {
        $authUser = $this->getAuthenticatedUserOrRedirect();
        if (!$authUser instanceof User) return $authUser;

        if ((int) $authUser->id !== (int) $id) {
            return redirect()->back()->withErrors(['error' => 'Tidak diizinkan mengubah data user lain']);
        }

        // Simpan status foto sebelumnya
        $hadPhoto = $authUser->photo !== null;

        // Hapus file foto dari storage jika ada
        if ($authUser->photo) {
            // Hapus file fisik
            $photoPath = $authUser->photo;
            if (Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
        }
        
        // Set photo menjadi null di database
        $authUser->photo = null;
        $authUser->save();

        // Update session
        session(['user' => [
            'id'    => $authUser->id,
            'name'  => $authUser->name,
            'role'  => $authUser->role,
            'photo' => null,
        ]]);

        $message = $hadPhoto ? 'Foto profil berhasil dihapus!' : 'Tidak ada foto untuk dihapus.';
        return redirect()->route('profile.edit')->with('success', $message);
    }

    // Reusable method to get user from JWT in session
    private function getAuthenticatedUserOrRedirect()
    {
        try {
            $token = JWTAuth::getToken() ?? session('jwt_token');
            if (!$token) {
                return redirect()->route('login.view')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            if (!$user) {
                return redirect()->route('login.view')->withErrors(['error' => 'User tidak ditemukan']);
            }

            return $user;
        } catch (TokenInvalidException | TokenExpiredException | JWTException $e) {
            return redirect()->route('login.view')->withErrors(['error' => 'Token tidak valid atau kedaluwarsa']);
        }
    }
}
