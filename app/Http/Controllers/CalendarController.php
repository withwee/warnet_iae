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

        Event::create($request->all());
        return Redirect::to('/admin/kalender')->with('success', 'Kegiatan berhasil ditambahkan!');
    }
}
