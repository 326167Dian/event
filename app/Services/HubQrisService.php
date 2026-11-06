<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien API cabang ke hub QRIS GDC Pay (mysifacenter.my.id) -- situs ini BUKAN
 * partner GDC terdaftar sendiri (GDC cuma izinkan 1 url callback per
 * partner), jadi QR dibuat/dicek lewat hub.
 */
class HubQrisService
{
    public function isConfigured(): bool
    {
        return filled(config('hubqris.api_key'));
    }

    /**
     * @return array{reference_no:string,qr_content:string,expires_at:string}
     */
    public function generateQr(string $referenceNo, int $amount): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Kredensial hub QRIS belum diisi.');
        }

        $response = $this->client()->post('/api/branch/qris/generate', [
            'branch_reference_no' => $referenceNo,
            'amount' => $amount,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Gagal membuat QR lewat hub: '.$response->status().' '.$response->body());
        }

        return $response->json();
    }

    /**
     * @return array{status:string}
     */
    public function queryStatus(string $referenceNo): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Kredensial hub QRIS belum diisi.');
        }

        $response = $this->client()->get('/api/branch/qris/'.$referenceNo.'/status');

        if ($response->failed()) {
            throw new RuntimeException('Gagal mengecek status QR lewat hub: '.$response->status().' '.$response->body());
        }

        return $response->json();
    }

    private function client()
    {
        return Http::baseUrl(rtrim(config('hubqris.base_url'), '/'))
            ->withHeaders(['X-Branch-Key' => config('hubqris.api_key')])
            ->timeout(15);
    }
}
