<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iuran;
use App\Models\Pengeluaran;
use App\Models\User;
use App\Models\Pengumuman;
use App\Models\Kegiatan;

class AdminController extends Controller
{
    public function dashboard()
{
    $totalIuran = Iuran::sum('total_bayar');
    $totalPengeluaran = Pengeluaran::sum('amount'); 
    $jumlahPengumuman = Pengumuman::count();
    $jumlahAcara = Kegiatan::count();
    $jumlahIuran = Iuran::count();
    $jumlahIuranTersisa = $totalIuran - $totalPengeluaran;
    $pengeluarans = Pengeluaran::latest()->get();

    return view('admin.dashboardAdmin', compact('totalIuran', 'totalPengeluaran', 'jumlahPengumuman', 'jumlahAcara', 'jumlahIuran', 'jumlahIuranTersisa', 'pengeluarans'));
}
    public function storePengeluaran(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Pengeluaran::create([
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return redirect()->route('admin.dashboardAdmin')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }
}