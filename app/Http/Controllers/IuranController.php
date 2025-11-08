<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iuran;
use App\Models\User;
use Midtrans\Config;
use Midtrans\Snap;

class IuranController extends Controller
{
    public function index(Request $request)
    {
        $iurans = [];
        $user = null;
        $no_kk = null;

        // Check if payment success data is passed via session or query
        if ($request->session()->has('payment_success_id')) {
            $idBayar = $request->session()->get('payment_success_id');
            $iuran = Iuran::find($idBayar);
            if ($iuran) {
                $user = $iuran->user;
                $iurans = [$iuran];
                $no_kk = $user ? $user->no_kk : null;
            }
            // Remove from session after use
            $request->session()->forget('payment_success_id');
        } elseif ($request->query('payment_success_id')) {
            $idBayar = $request->query('payment_success_id');
            $iuran = Iuran::find($idBayar);
            if ($iuran) {
                $user = $iuran->user;
                $iurans = [$iuran];
                $no_kk = $user ? $user->no_kk : null;
            }
        }

        return view('pay', compact('iurans', 'user', 'no_kk'));
    }

    public function transaction()
    {
        return view('succespay');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_iuran' => 'required|string|max:255',
            'total_bayar' => 'required|numeric|min:0',
        ]);

        // Ambil semua data pengguna (nomor KK)
        $users = User::all();

        // Loop melalui setiap pengguna dan tambahkan data iuran
        foreach ($users as $user) {
            Iuran::create([
                'user_id' => $user->id,
                'jenis_iuran' => $request->jenis_iuran,
                'total_bayar' => $request->total_bayar,
                'status' => 'Belum Bayar',
            ]);

            // Kirim notifikasi ke user (kecuali admin)
            if ($user->role !== 'admin') {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'iuran',
                    'message' => 'Ayo bayar iuranmu! Anda mendapatkan tagihan iuran sebesar Rp. ' . number_format($request->total_bayar, 0, ',', '.') . ', mohon segera dibayar.',
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
        }

        // Redirect based on user role
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.bayar-iuran')->with('success', 'Data iuran berhasil ditambahkan untuk semua pengguna.');
        }
        return redirect()->route('bayar-iuran')->with('success', 'Data iuran berhasil ditambahkan untuk semua pengguna.');
    }

    public function cari(Request $request)
    {
        $no_kk = $request->no_kk;

        // Validasi no_kk kosong atau tidak valid
        if (empty($no_kk) || !is_string($no_kk)) {
            return redirect()->route('bayar-iuran')->with('error', 'no kk tidak valid silakan isi yg valid');
        }

        // Cari user berdasarkan no_kk
        $user = User::where('no_kk', $no_kk)->first();

        if (!$user) {
            return redirect()->route('bayar-iuran')->with('error', 'no kk tidak valid silakan isi yg valid');
        }

        // Ambil iuran berdasarkan user_id
        $iurans = Iuran::where('user_id', $user->id)->get();

        return view('pay', compact('iurans', 'user', 'no_kk'));
    }

    public function createPaymentLink($id)
    {
        $iuran = Iuran::find($id);

        if (!$iuran) {
            return redirect()->back()->with('error', 'Data iuran tidak ditemukan.');
        }

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Data untuk Payment Link
        $params = [
            'transaction_details' => [
                'order_id' => 'IURAN-' . $iuran->id_bayar . '-' . time(),
                'gross_amount' => $iuran->total_bayar,
            ],
            'item_details' => [
                [
                    'id' => $iuran->id_bayar,
                    'price' => $iuran->total_bayar,
                    'quantity' => 1,
                    'name' => $iuran->jenis_iuran,
                ],
            ],
            'customer_details' => [
                'first_name' => $iuran->user->name,
                'email' => $iuran->user->email,
            ],
        ];

        // Buat Payment Link
        try {
            $paymentLink = Snap::createTransaction($params);
            return redirect($paymentLink->redirect_url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat Payment Link: ' . $e->getMessage());
        }
    }

    public function getSnapToken($id)
{
    $iuran = Iuran::with('user')->find($id);

    if (!$iuran || !$iuran->user || !$iuran->user->email) {
        return response()->json(['error' => 'Data iuran atau pengguna tidak valid.'], 404);
    }

    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    $params = [
        'transaction_details' => [
            'order_id' => 'IURAN-' . $iuran->id_bayar . '-' . time(),
            'gross_amount' => $iuran->total_bayar,
        ],
        'customer_details' => [
            'first_name' => $iuran->user->name,
            'email' => $iuran->user->email,
        ],
        'item_details' => [[
            'id' => $iuran->id_bayar,
            'price' => $iuran->total_bayar,
            'quantity' => 1,
            'name' => $iuran->jenis_iuran,
        ]],
    ];

    try {
        $snapToken = Snap::getSnapToken($params);
        return response()->json(['snapToken' => $snapToken]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function bayar($id)
    {
        $iuran = Iuran::find($id);

        if (!$iuran) {
            return redirect()->back()->with('error', 'Data iuran tidak ditemukan.');
        }

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Data transaksi
    $params = [
    'transaction_details' => [
        'order_id' => 'IURAN-' . $iuran->id_bayar . '-' . time(), 
        'gross_amount' => $iuran->total_bayar,
    ],
    'customer_details' => [
        'first_name' => $iuran->user->name,
        'email' => $iuran->user->email,
    ],
];
        // Buat transaksi
        $snapToken = Snap::getSnapToken($params);

        return view('pay', compact('snapToken', 'iuran'));
    }

    public function success($id)
    {
        $idBayar = $id;
        if (strpos($id, 'IURAN-') === 0) {
            $parts = explode('-', $id);
            if (count($parts) >= 2) {
                $idBayar = $parts[1];
            }
        }

        $iuran = Iuran::findOrFail($idBayar);
        $iuran->status = 'Sudah Bayar';
        $iuran->save();
        return redirect()->route('bayar-iuran')->with([
            'success' => 'Pembayaran berhasil. Status iuran telah diperbarui.',
            'payment_success_id' => $idBayar,
        ]);
    }


    public function notificationHandler(Request $request)
    {
        $notif = new Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        $iuran = Iuran::findOrFail($orderId);

        if ($transaction == 'capture') {
            if ($type == 'credit_card' && $fraud == 'challenge') {
                $iuran->status = 'challenge';
            } else {
                $iuran->status = 'success';
            }
        } elseif ($transaction == 'settlement') {
            $iuran->status = 'success';
        } elseif ($transaction == 'pending') {
            $iuran->status = 'pending';
        } elseif (in_array($transaction, ['deny', 'cancel'])) {
            $iuran->status = 'failed';
        } elseif ($transaction == 'expire') {
            $iuran->status = 'expired';
        }

        $iuran->save();
    }
}