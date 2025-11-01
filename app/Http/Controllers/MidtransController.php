<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iuran;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        \Log::info('Callback Midtrans received:', $request->all());

        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            \Log::warning('Invalid signature in Midtrans callback', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Parse order_id to extract id_bayar
        $orderIdParts = explode('-', $request->order_id);
        if (count($orderIdParts) < 2) {
            \Log::warning('Invalid order_id format in Midtrans callback', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Invalid order_id format'], 400);
        }
        $idBayar = $orderIdParts[1];
        \Log::info('Extracted id_bayar from order_id', ['id_bayar' => $idBayar]);

        $iuran = Iuran::where('id_bayar', $idBayar)->first();

        if (!$iuran) {
            \Log::error('Iuran record not found for id_bayar', ['id_bayar' => $idBayar]);
            return response()->json(['message' => 'Iuran record not found'], 404);
        }

        $status = $request->transaction_status;
        \Log::info('Midtrans transaction status received', ['status' => $status]);

        if ($status === 'settlement') {
            $iuran->status = 'Sudah Bayar';
        } elseif ($status === 'pending') {
            $iuran->status = 'pending';
        } elseif (in_array($status, ['expire', 'cancel', 'deny'])) {
            $iuran->status = 'Expired';
        } else {
            \Log::warning('Unhandled transaction status in Midtrans callback', ['status' => $status]);
        }

        $iuran->save();
        \Log::info('Iuran status updated successfully', ['id_bayar' => $idBayar, 'new_status' => $iuran->status]);

        return response()->json(['message' => 'Callback processed'], 200);
    }
}

