<?php

namespace App\Http\Controllers;

use App\Models\Event;


class EventController extends Controller
{
    public function index()
    {
        $activeEvent = Event::query()
            ->where('is_active', 1)
            ->latest('id')
            ->first();

        if ($activeEvent) {
            return redirect()->route('events.show', $activeEvent);
        }

        $events = Event::orderBy('start_at', 'asc')->where('is_active', 1)->get();

        return view('frontend.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        return view('frontend.events.show', compact('event'));
    }
}
