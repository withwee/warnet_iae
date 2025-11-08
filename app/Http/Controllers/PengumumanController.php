<?php
// app/Http/Controllers/PengumumanController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengumuman;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class PengumumanController extends Controller
{
    // Tampilkan semua pengumuman
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
        }

        $pengumumans = Pengumuman::latest()->get();

        if ($user->role === 'admin') {
            return view('admin.pengumumanAdmin', compact('pengumumans'));
        }

        $pengumumanKhusus = Pengumuman::where('pengumuman_khusus', true)->latest()->first();
        $pengumumans = Pengumuman::where('pengumuman_khusus', false)->latest()->get();

        return view('pengumuman', compact('pengumumanKhusus', 'pengumumans'));
    }

    // Simpan pengumuman baru
    public function store(Request $request)
    {
        $request->validate([
            'judulPengumuman' => 'required|string|min:3',
            'isiPengumuman' => 'required|string|min:3',
        ]);

        if ($request->has('pengumuman_khusus')) {
            // Reset semua pengumuman khusus
            Pengumuman::where('pengumuman_khusus', true)->update(['pengumuman_khusus' => false]);
        }

        // Simpan pengumuman baru (global untuk semua user)
        $pengumuman = Pengumuman::create([
            'judulPengumuman' => $request->judulPengumuman,
            'isiPengumuman' => $request->isiPengumuman,
            'pengumuman_khusus' => $request->has('pengumuman_khusus'),
        ]);

        // Kirim notifikasi ke semua user (kecuali admin)
        $users = \App\Models\User::where('role', '!=', 'admin')->get();
        foreach ($users as $user) {
            \App\Models\notification::create([
                'user_id' => $user->id,
                'type' => 'pengumuman',
                'message' => 'Ada pengumuman baru: ' . $pengumuman->judulPengumuman,
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
            return redirect()->route('admin.pengumuman')
                   ->with('success', 'Pengumuman berhasil ditambahkan dan akan tampil untuk semua user!');
        }

        return redirect()->route('pengumuman')->with('success', 'Pengumuman berhasil ditambahkan!');
    }

    //     $pengumuman = Pengumuman::create([
    //     'judul' => $request->judul,
    //     'isi' => $request->isi,
    //     ]);
    
    //     return redirect()->route('pengumuman')->with('success', 'Pengumuman berhasil dibuat!');
    // }

    // Update pengumuman
    public function update(Request $request, $id)
    {
        $request->validate([
            'judulPengumuman' => 'required|string|min:3|max:255',
            'isiPengumuman' => 'required|string|min:10',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update($request->only(['judulPengumuman', 'isiPengumuman']));

        return redirect()->route('pengumuman')->with('success', 'Pengumuman berhasil diperbarui!');
    }

    // Hapus pengumuman
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return redirect()->route('pengumuman')->with('success', 'Pengumuman berhasil dihapus!');
    }

    // Ubah status pengumuman khusus
    public function toggleKhusus($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if (!$pengumuman->pengumuman_khusus) {
            // Reset pengumuman_khusus lain
            Pengumuman::where('pengumuman_khusus', true)->update(['pengumuman_khusus' => false]);
        }

        $pengumuman->pengumuman_khusus = !$pengumuman->pengumuman_khusus;
        $pengumuman->save();

        return redirect()->route('pengumuman')->with('success', 'Status pengumuman berhasil diubah!');
    }

}
