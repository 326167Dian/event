<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Mengirim event ke Meta Conversions API (server-side).
 * Aktif hanya jika Pixel ID + Access Token diisi di halaman admin › Pengaturan.
 */
class MetaConversionsService
{
    protected const GRAPH_VERSION = 'v21.0';

    public function isEnabled(): bool
    {
        return Setting::metaPixelId() !== null && filled(Setting::get('meta_capi_token'));
    }

    /**
     * Kirim event Purchase untuk pendaftaran yang sudah lunas.
     * event_id = order_id, sama dengan yang dikirim pixel di browser, supaya Meta tidak menghitung dobel.
     */
    public function sendPurchase(Registration $registration): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $registration->loadMissing('event');
        $tracking = (array) ($registration->meta_tracking ?? []);

        $userData = array_filter([
            'em' => $this->hash(strtolower(trim((string) $registration->email))),
            'ph' => $this->hash(preg_replace('/\D/', '', (string) $registration->phone)),
            'fn' => $this->hash(strtolower(trim((string) strtok((string) $registration->fullname, ' ')))),
            'external_id' => $this->hash((string) $registration->user_id),
            'country' => $this->hash('id'),
            'client_ip_address' => $tracking['ip'] ?? null,
            'client_user_agent' => $tracking['user_agent'] ?? null,
            'fbp' => $tracking['fbp'] ?? null,
            'fbc' => $tracking['fbc'] ?? null,
        ]);

        $payload = [
            'data' => [[
                'event_name' => 'Purchase',
                'event_time' => ($registration->paid_at ?? now())->timestamp,
                'event_id' => $registration->tracking_event_id,
                'action_source' => 'website',
                'event_source_url' => route('events.show', $registration->event_id),
                'user_data' => $userData,
                'custom_data' => [
                    'currency' => 'IDR',
                    'value' => (float) $registration->amount,
                    'content_name' => $registration->event?->title,
                    'content_ids' => ['EVENT-' . $registration->event_id],
                    'content_type' => 'product',
                ],
            ]],
            'access_token' => Setting::get('meta_capi_token'),
        ];

        if ($testCode = Setting::get('meta_test_event_code')) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(8)->asJson()->post(
                'https://graph.facebook.com/' . static::GRAPH_VERSION . '/' . Setting::metaPixelId() . '/events',
                $payload
            );

            if ($response->failed()) {
                Log::warning('Meta CAPI: gagal kirim Purchase', [
                    'registration_id' => $registration->id,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Meta CAPI: error kirim Purchase', [
                'registration_id' => $registration->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function hash(?string $value): ?string
    {
        return filled($value) ? hash('sha256', $value) : null;
    }
}
