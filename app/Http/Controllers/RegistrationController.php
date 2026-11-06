<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $isPaidEvent = $event->price > 0;
        $mode = Setting::paymentMode(); // qris (otomatis, dgn fallback) | manual

        // 'manual' di sini berarti: form upload bukti transfer/QRIS statis --
        // dikirim langsung saat admin memaksa mode manual, ATAU saat peserta
        // mengirim form fallback di halaman event (lihat events/show.blade.php)
        // setelah QRIS Dinamis gagal. 'qris' berarti: lanjut ke payments.show,
        // yang akan mencoba QRIS Dinamis lalu jatuh ke fallback kalau perlu.
        $method = null;
        if ($isPaidEvent) {
            $method = ($mode === 'manual' || $request->input('payment_method') === 'manual')
                ? 'manual'
                : 'qris';
        }

        $request->validate([
            'name' => [$isPaidEvent ? 'required' : 'nullable', 'string', 'max:255'],
            'no_tlp' => [$isPaidEvent ? 'required' : 'nullable', 'string', 'max:20'],
            'payment_method' => ['nullable', Rule::in(['qris', 'manual'])],
            'foto' => [$method === 'manual' ? 'required' : 'nullable', 'image', 'max:5120'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'no_tlp.required' => 'No. WhatsApp wajib diisi.',
            'foto.required' => 'Bukti transfer wajib diupload untuk pembayaran transfer manual.',
            'foto.image' => 'Bukti transfer harus berupa file gambar.',
            'foto.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);

        $user = auth()->user();

        $fullname = $isPaidEvent ? $request->name : $user->name;
        $phone = $isPaidEvent ? '+62' . ltrim(preg_replace('/\D/', '', $request->no_tlp), '0') : $user->no_tlp;
        if ($isPaidEvent && str_starts_with($phone, '+6262')) {
            $phone = '+62' . substr($phone, 5);
        }

        if ($isPaidEvent) {
            $user->forceFill(['name' => $fullname, 'no_tlp' => $phone])->save();
        }

        $fotoPath = ($method === 'manual' && $request->hasFile('foto'))
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
                'payment_method' => $method,
                // Event gratis langsung disetujui. QRIS disetujui otomatis oleh webhook Midtrans,
                // transfer manual menunggu verifikasi admin.
                'status' => $isPaidEvent ? 'waiting_approval' : 'approved',
                'meta_tracking' => [
                    'fbp' => $request->cookie('_fbp'),
                    'fbc' => $request->cookie('_fbc'),
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                ],
            ]
        );

        if ($method === 'qris') {
            return redirect()->route('payments.show', $registration);
        }

        $message = $isPaidEvent
            ? 'Pendaftaran berhasil! Bukti transfer kamu sedang diverifikasi oleh admin.'
            : 'Pendaftaran berhasil!';

        return redirect()->route('events.show', $event)
            ->with('success', $message)
            ->with('pixel_lead', ! $isPaidEvent);
    }
}
