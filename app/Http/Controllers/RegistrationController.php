<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;


class RegistrationController extends Controller
{
    public function store(Event $event)
    {
        $orderId = Registration::max('order_id');
        $user = auth()->user();
        $orderId = 'EVT-' . time();

        Registration::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'amount' => $event->price,
                'order_id' => $orderId,

            ],
            [
                'fullname' => $user->name,
                'phone' => $user->no_tlp, // pastikan kolom ini ada di tabel users
                'email' => $user->email,
            ]
        );

        return redirect()->route('events.show', $event)
            ->with('success', 'Pendaftaran berhasil! Silakan konfirmasi pembayaran via WhatsApp admin.');
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        \Log::info('🔥 CALLBACK MASUK 🔥', $request->all());

        if ($hashed === $request->signature_key) {
            $registration = Registration::where('order_id', trim($request->order_id))->first();

            if ($registration) {
                $transactionStatus = $request->transaction_status;
                \Log::info('Transaction status: ' . $transactionStatus);

                if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                    \Log::info('✅ Transaction settled, updating to approved');
                    $updated = $registration->update(['status' => 'approved']);
                    \Log::info('Update result: ' . json_encode($updated));
                } elseif ($transactionStatus === 'pending') {
                    \Log::info('🕒 Transaction pending');
                    $registration->update(['status' => 'pending']);
                } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                    \Log::info('❌ Transaction failed');
                    $registration->update(['status' => 'failed']);
                }
            } else {
                \Log::warning('❌ Registration not found for order_id: ' . $request->order_id);
            }
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }
}
