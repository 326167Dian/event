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
            'name' => [$isPaidEvent ? 'required' : 'nullable', 'string', 'max:255'],
            'no_tlp' => [$isPaidEvent ? 'required' : 'nullable', 'string', 'max:20'],
            'foto' => [$isPaidEvent ? 'required' : 'nullable', 'image', 'max:5120'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'no_tlp.required' => 'No. telepon wajib diisi.',
            'foto.required' => 'Bukti transfer wajib diupload untuk event berbayar.',
            'foto.image' => 'Bukti transfer harus berupa file gambar.',
            'foto.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);

        $user = auth()->user();

        $fullname = $isPaidEvent ? $request->name : $user->name;
        $phone = $isPaidEvent ? '+62' . ltrim($request->no_tlp, '0') : $user->no_tlp;

        if ($isPaidEvent) {
            $user->forceFill(['name' => $fullname, 'no_tlp' => $phone])->save();
        }

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
                'fullname' => $fullname,
                'phone' => $phone,
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
