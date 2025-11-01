@extends('layouts.app')

@section('content')
<h2>Kalender</h2>
<h3>Maret, 2025</h3>
<div class="calendar">
    @foreach($events as $event)
        <div class="event" style="grid-row: auto; background-color: {{ $event->title === 'Menanam Pohon Bersama' ? '#DFFFD8' : ($event->title === 'Pertemuan Bulanan Warga' ? '#E4D9FF' : '#FFD6D6') }}">
            <strong>{{ date('j', strtotime($event->event_date)) }}</strong><br>
            {{ $event->title }}
        </div>
    @endforeach
</div>
@endsection