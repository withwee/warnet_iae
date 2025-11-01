<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;


class KegiatanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judulKegiatan' => 'required|string|min:5|max:100',
            'deskripsiKegiatan' => 'required|string|min:10|max:500',
            'tanggalKegiatan' => 'required|date|after_or_equal:today',
        ]);

        Kegiatan::create([
            'judul' => $validated['judulKegiatan'],
            'deskripsi' => $validated['deskripsiKegiatan'],
            'tanggal' => $validated['tanggalKegiatan'],
        ]);

        return redirect()->route('kalender')
               ->with('success', 'Kegiatan berhasil dibuat');
    }

    
}