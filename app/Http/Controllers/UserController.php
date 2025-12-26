<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Kegiatan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;


class UserController extends Controller
{
    // Tampilkan form registrasi
    public function register()
    {
        return view('register');
    }

    // Proses data registrasi
    public function registerSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|min:3',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'nik'               => 'required|digits:16|unique:users,nik',
            'no_kk'             => 'required|digits:16',
            'phone'             => 'required|string|min:10',
            'jumlah_LK'         => 'required|integer|min:0',
            'jumlah_PR'         => 'required|integer|min:0',
            'photo'             => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'nik'               => $request->nik,
            'no_kk'             => $request->no_kk,
            'phone'             => $request->phone,
            'role'              => 'user',
            'jumlah_LK'         => $request->jumlah_LK,
            'jumlah_PR'         => $request->jumlah_PR,
        ]);

        return redirect()->route('login.view')->with('message', 'Registrasi berhasil. Silakan login.');
    }

    // ADMIN LOGIN
    public function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|min:5',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['error' => 'Nama pengguna atau kata sandi salah.'])->withInput();
        }

        if (User::count() == 0) {
            User::create([
                'name'     => 'admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'nik'      => '1234567890123459',
                'no_kk'    => '1234567890123459',
                'phone'    => '08123456789',
                'jumlah_keluarga' => 1,
                'role' => 'admin'
            ]);
        }

        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['error' => 'Username atau Password salah'])->withInput();
        }

        $token = JWTAuth::fromUser($user);

        session([
            'jwt_token' => $token,
            'user' => [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'photo' => $user->photo
            ],
        ]);

        return redirect()->route('admin.dashboardAdmin')->with('message', 'Login berhasil');
    }

    // USER LOGIN
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password', 'nik');

        $validator = Validator::make($credentials, [
            'email'    => 'required_without:nik|email',
            'nik'      => 'required_without:email|digits:16',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = $request->has('email') 
            ? User::where('email', $credentials['email'])->first()
            : User::where('nik', $credentials['nik'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
            return back()->withErrors(['error' => 'NIK/Email atau Password salah'])->withInput();
        }

        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => $user
            ]);
        }

        session([
            'jwt_token' => $token,
            'user' => [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'photo' => $user->photo
            ],
        ]);

        return redirect()->route('dashboard')->with('message', 'Login berhasil');
    }

    // LOGOUT
    public function logout()
    {
        $token = session('jwt_token');
        if ($token) {
            try {
                JWTAuth::invalidate($token);
            } catch (JWTException $e) {
                // abaikan jika token invalid
            }
        }

        session()->forget('jwt_token');

        return redirect()->route('home')->with('message', 'Logout berhasil');
    }

   public function dashboard(Request $request)
{
    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login')->withErrors(['error' => 'Silakan login terlebih dahulu']);
    }

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboardAdmin');
    }

    // ✅ Ambil year & month dari query string, fallback ke sekarang
    $year = $request->input('year', now()->year);
    $month = $request->input('month', now()->month);
    $totalIuran = Iuran::sum('total_bayar');
    $totalPengeluaran = Pengeluaran::sum('amount'); 
    // Ambil pengumuman khusus terbaru untuk dashboard user
    $pengumumanTerbaru = Pengumuman::where('pengumuman_khusus', true)->latest()->first();
    $kalenderKegiatan = Kegiatan::whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year)
                                ->get();
    $jumlahIuran = Iuran::count();
    $jumlahKK = User::distinct('no_kk')->count('no_kk');
    $totalLakiLaki = User::sum('jumlah_LK');
    $totalPerempuan = User::sum('jumlah_PR');
    $totalWarga = $totalLakiLaki + $totalPerempuan;

    return view('dashboard', compact(
        'user', 
        'kalenderKegiatan', 
        'year', 
        'month',
        'totalIuran', 
        'totalPengeluaran', 
        'pengumumanTerbaru',
        'jumlahIuran',
        'totalLakiLaki', 
        'totalPerempuan',
        'totalWarga', 
        'jumlahKK'
    ));
}

    // PENGUMUMAN (user & admin)
    public function pengumuman()
    {
        $user = $this->getAuthenticatedUserOrRedirect();
        if (!$user instanceof User) return $user;

        $pengumumans = Pengumuman::latest()->get();

        if ($user->role === 'admin') {
            return view('admin.pengumumanAdmin', compact('pengumumans'));
        }

        return view('pengumuman', compact('pengumumans'));
    }

    public function forum()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Silakan login terlebih dahulu']);
        }

        $forums = \App\Models\Forum::with(['user', 'comments.user'])->latest()->get();
        
        return view($user->role === 'admin' ? 'admin.forumAdmin' : 'forum', compact('user', 'forums'));
    }

    public function bayarIuran()
    {
        $user = $this->getAuthenticatedUserOrRedirect();
        if (!$user instanceof User) return $user;

        return view('pay');
    }

  public function kalender(Request $request)
{
    $user = $this->getAuthenticatedUserOrRedirect();
    if (!$user instanceof User) return $user;

    $month = $request->input('month');
    $year = $request->input('year');

    $currentDate = Carbon::now();
    if ($month && $year) {
        $currentDate = Carbon::createFromDate($year, $month, 1);
    }

    $kalendars = Kegiatan::whereMonth('tanggal', $currentDate->month)
        ->whereYear('tanggal', $currentDate->year)
        ->get();

    return view($user->role === 'admin' ? 'admin.kalenderAdmin' : 'kalender', compact('kalendars', 'currentDate'));
}


    public function pembayaran()
    {
        $user = $this->getAuthenticatedUserOrRedirect();
        if (!$user instanceof User) return $user;

        return view('pay');
    }

    public function notifikasi()
    {
        $user = $this->getAuthenticatedUserOrRedirect();
        if (!$user instanceof User) return $user;

        // Ambil notifikasi user
        $notifikasis = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifikasi', compact('notifikasis', 'user'));
    }

    // 🔐 Ambil user dari Auth
    private function getAuthenticatedUserOrRedirect()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
        }

        return $user;
    }

    // API Methods
    public function notifikasiApi()
    {
        $user = auth()->user();
        $notifikasis = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifikasis);
    }
}