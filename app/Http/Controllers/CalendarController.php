<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
{
    $currentDate = Carbon::now();
    $month = $currentDate->month;
    $year = $currentDate->year;

    $eventsRaw = Event::whereMonth('date', $month)
                      ->whereYear('date', $year)
                      ->get();

    // Ubah ke format: [tanggal => [title]]
    $events = [];
    foreach ($eventsRaw as $event) {
        $day = Carbon::parse($event->date)->day;
        $events[$day] = $event->title;
    }

    return view('dashboard', [
        'currentDate' => $currentDate,
        'events' => $events
    ]);
}


    public function adminIndex()
    {
        $events = Event::all();
        return View::make('calendar.admin', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'event_date' => 'required|date',
        ]);

        $event = Event::create($request->all());

        // Send notification to all users except admin
        $users = \App\Models\User::where('role', '!=', 'admin')->get();
        foreach ($users as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'kalender',
                'message' => 'Acara baru "' . $event->title . '" telah ditambahkan pada tanggal ' . \Carbon\Carbon::parse($event->date)->format('d M Y'),
            ]);

            // Keep only 5 latest notifications
            $notifToDelete = \App\Models\Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->skip(5)
                ->take(PHP_INT_MAX)
                ->get();

            foreach ($notifToDelete as $notif) {
                $notif->delete();
            }
        }

        return Redirect::to('/admin/kalender')->with('success', 'Kegiatan berhasil ditambahkan!');
    }
}
