<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $isPaidEvent = $event->price > 0;

        $request->validate([
            'foto' => [$isPaidEvent ? 'required' : 'nullable', 'image', 'max:5120'],
        ], [
            'foto.required' => 'Bukti transfer wajib diupload untuk event berbayar.',
            'foto.image' => 'Bukti transfer harus berupa file gambar.',
            'foto.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);

        $user = auth()->user();

        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('bukti-transfer', 'public')
            : null;

        $registration = Registration::updateOrCreate(
            [
                'user_id' => $user->id,
                'event_id' => $event->id,
            ],
            [
                'amount' => $event->price,
                'fullname' => $user->name,
                'phone' => $user->no_tlp,
                'email' => $user->email,
                'foto' => $fotoPath,
                // Event gratis langsung disetujui, event berbayar menunggu verifikasi admin.
                'status' => $isPaidEvent ? 'waiting_approval' : 'approved',
            ]
        );

        $message = $isPaidEvent
            ? 'Pendaftaran berhasil! Bukti transfer kamu sedang diverifikasi oleh admin.'
            : 'Pendaftaran berhasil!';

        return redirect()->route('events.show', $event)->with('success', $message);
    }
}
