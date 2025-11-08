<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;


class KegiatanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judulKegiatan' => 'required|string|min:3|max:100',
            'deskripsiKegiatan' => 'required|string|min:3|max:500',
            'tanggalKegiatan' => 'required|date|after_or_equal:today',
        ]);

        // Create kegiatan (global untuk semua user)
        $kegiatan = Kegiatan::create([
            'judul' => $validated['judulKegiatan'],
            'deskripsi' => $validated['deskripsiKegiatan'],
            'tanggal' => $validated['tanggalKegiatan'],
        ]);

        // Kirim notifikasi ke semua user (kecuali admin)
        $users = \App\Models\User::where('role', '!=', 'admin')->get();
        foreach ($users as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'kalender',
                'message' => 'Ada acara baru: ' . $kegiatan->judul . ' pada tanggal ' . \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y'),
            ]);

            // Hapus notifikasi lama, sisakan 5 terbaru
            $notifToDelete = \App\Models\Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->skip(5)
                ->take(PHP_INT_MAX)
                ->get();

            foreach ($notifToDelete as $notif) {
                $notif->delete();
            }
        }

        // Redirect based on user role
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.kalender')
                   ->with('success', 'Kegiatan berhasil dibuat dan akan tampil di kalender semua user');
        }
        
        return redirect()->route('kalender')
               ->with('success', 'Kegiatan berhasil dibuat');
    }

    
}