<?php

namespace App\Services;

use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\CoreApi;

class MidtransService
{
    /**
     * Status transaksi Midtrans yang dianggap final (tidak akan berubah lagi).
     */
    public const FINAL_STATUSES = ['settlement', 'capture', 'expire', 'cancel', 'deny', 'failure'];

    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Buat (atau pakai ulang) transaksi QRIS untuk sebuah pendaftaran.
     */
    public function createQrisTransaction(Registration $registration): Registration
    {
        // Kalau masih ada QR yang belum expired dan belum final, pakai ulang saja.
        if (
            $registration->order_id
            && $registration->qr_url
            && ! in_array($registration->payment_status, self::FINAL_STATUSES, true)
            && $registration->expired_at
            && $registration->expired_at->isFuture()
        ) {
            return $registration;
        }

        $orderId = 'EVT-' . $registration->id . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
        $amount = (int) round((float) $registration->amount);

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'qris' => [
                'acquirer' => 'gopay',
            ],
            'customer_details' => [
                'first_name' => $registration->fullname,
                'email' => $registration->email,
                'phone' => $registration->phone,
            ],
            'item_details' => [[
                'id' => 'EVENT-' . $registration->event_id,
                'price' => $amount,
                'quantity' => 1,
                'name' => Str::limit($registration->event?->title ?? 'Tiket Seminar', 45, ''),
            ]],
        ];

        $response = $this->chargeWithFallback($params);

        $qrUrl = null;
        foreach ((array) ($response['actions'] ?? []) as $action) {
            $action = (array) $action;
            if (($action['name'] ?? null) === 'generate-qr-code') {
                $qrUrl = $action['url'] ?? null;
                break;
            }
        }

        $registration->update([
            'payment_method' => 'qris',
            'order_id' => $orderId,
            'transaction_id' => $response['transaction_id'] ?? null,
            'payment_status' => $response['transaction_status'] ?? 'pending',
            'qr_url' => $qrUrl,
            'paid_at' => null,
            'expired_at' => isset($response['expiry_time'])
                ? Carbon::parse($response['expiry_time'])
                : now()->addMinutes(15),
            'midtrans_response' => $response,
        ]);

        return $registration->fresh();
    }

    /**
     * Panggil CoreApi::charge, dan kalau gagal karena channel/acquirer tertentu
     * belum aktif di akun Midtrans, coba ulang tanpa memaksa acquirer spesifik.
     */
    protected function chargeWithFallback(array $params): array
    {
        try {
            return (array) CoreApi::charge($params);
        } catch (\Exception $e) {
            $isChannelError = str_contains($e->getMessage(), 'not activated')
                || str_contains($e->getMessage(), '402');

            if ($isChannelError && isset($params['qris'])) {
                Log::warning('Midtrans: charge dengan acquirer spesifik gagal, mencoba tanpa acquirer.', [
                    'message' => $e->getMessage(),
                ]);

                unset($params['qris']);

                try {
                    return (array) CoreApi::charge($params);
                } catch (\Exception $e2) {
                    $e = $e2;
                }
            }

            Log::error('Midtrans: gagal membuat transaksi QRIS.', ['message' => $e->getMessage()]);

            throw new \RuntimeException(
                'Pembayaran QRIS belum bisa diproses oleh Midtrans. Kemungkinan channel GoPay/QRIS '
                    . 'belum diaktifkan di Dashboard Midtrans (menu Settings > Configuration). '
                    . 'Detail teknis: ' . $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * Proses payload notifikasi/webhook dari Midtrans.
     * Mengembalikan Registration yang diupdate, atau null kalau payload tidak valid.
     */
    public function handleNotification(array $payload): ?Registration
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signatureKey) {
            Log::warning('Midtrans notification: payload tidak lengkap', $payload);

            return null;
        }

        $serverKey = (string) config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (! hash_equals($expectedSignature, (string) $signatureKey)) {
            Log::warning('Midtrans notification: signature_key tidak valid', ['order_id' => $orderId]);

            return null;
        }

        $registration = Registration::where('order_id', $orderId)->first();

        if (! $registration) {
            Log::warning('Midtrans notification: registration tidak ditemukan', ['order_id' => $orderId]);

            return null;
        }

        $transactionStatus = $payload['transaction_status'] ?? null;

        $registration->payment_status = $transactionStatus;
        $registration->midtrans_response = $payload;

        if (in_array($transactionStatus, ['settlement', 'capture'], true)) {
            $registration->status = 'approved';
            $registration->paid_at = now();
        }

        $registration->save();

        return $registration;
    }
}
