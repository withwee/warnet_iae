<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotifController extends Controller
{
    public function index()
    {
        $notifs = Notification::where('user_id', auth()->id())->latest()->get();
        // Tandai semua sebagai sudah dibaca
        Notification::where('user_id', auth()->id())->update(['read' => true]);
        return view('notif', compact('notifs'));
    }
}