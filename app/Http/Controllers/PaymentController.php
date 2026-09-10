<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman QR code pembayaran untuk sebuah pendaftaran.
     */
    public function show(Registration $registration, MidtransService $midtrans)
    {
        abort_unless($registration->user_id === auth()->id(), 403);

        if ($registration->status === 'approved') {
            return redirect()->route('events.show', $registration->event_id)
                ->with('success', 'Pendaftaran kamu sudah dikonfirmasi.');
        }

        try {
            $registration = $midtrans->createQrisTransaction($registration);
        } catch (\Throwable $e) {
            return redirect()->route('events.show', $registration->event_id)
                ->with('error', $e->getMessage());
        }

        return view('payments.qris', compact('registration'));
    }

    /**
     * Endpoint AJAX untuk polling status pembayaran dari halaman QR.
     */
    public function status(Registration $registration)
    {
        abort_unless($registration->user_id === auth()->id(), 403);

        return response()->json([
            'payment_status' => $registration->payment_status,
            'status' => $registration->status,
        ]);
    }

    /**
     * Webhook notifikasi dari Midtrans. Route ini dikecualikan dari CSRF.
     */
    public function notification(Request $request, MidtransService $midtrans)
    {
        $midtrans->handleNotification($request->all());

        return response()->json(['message' => 'OK']);
    }
}
