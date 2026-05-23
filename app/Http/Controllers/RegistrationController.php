<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;


class RegistrationController extends Controller
{
    public function store(Event $event)
    {
        $user = auth()->user();

        Registration::updateOrCreate(
            [
                'user_id' => $user->id,
                'event_id' => $event->id,
            ],
            [
                'amount' => $event->price,
                'fullname' => $user->name,
                'phone' => $user->no_tlp,
                'email' => $user->email,
            ]
        );

        return redirect()->route('events.show', $event)
            ->with('success', 'Pendaftaran berhasil! Silakan konfirmasi pembayaran via WhatsApp admin.');
    }
}
