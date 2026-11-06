<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Setting;
use App\Services\HubQrisService;
use App\Services\MidtransService;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(private readonly HubQrisService $hubQris)
    {
    }

    /**
     * Tampilkan halaman pembayaran untuk sebuah pendaftaran. Satu tampilan
     * saja untuk peserta: QRIS Dinamis (GDC lewat hub) diutamakan; kalau
     * gagal dibuat, otomatis beralih ke QRIS statis (gambar upload admin),
     * lalu transfer manual -- tidak pernah menampilkan error atau pilihan
     * metode ke peserta.
     */
    public function show(Request $request, Registration $registration)
    {
        abort_unless($registration->user_id === auth()->id(), 403);

        if ($registration->status === 'approved') {
            return redirect()->route('events.show', $registration->event_id)
                ->with('success', 'Pendaftaran kamu sudah dikonfirmasi.');
        }

        if (Setting::paymentMode() !== 'manual') {
            $qrImageSrc = $this->ensureGdcQr($registration);

            if ($qrImageSrc) {
                return view('payments.qris', [
                    'registration' => $registration,
                    'qrImageSrc' => $qrImageSrc,
                ]);
            }
        }

        // QRIS Dinamis tidak tersedia/gagal -- tampilkan fallback (QRIS
        // statis kalau admin sudah upload, atau transfer manual) langsung di
        // halaman event, bukan di sini, supaya satu tempat saja yang peserta
        // lihat untuk melanjutkan pembayaran.
        return redirect()->route('events.show', $registration->event_id)
            ->with('qris_dynamic_unavailable', true);
    }

    /**
     * Buat (atau pakai ulang) QR GDC lewat hub. Null kalau gagal/tidak
     * dikonfigurasi -- caller lalu jatuh ke fallback statis/manual.
     */
    private function ensureGdcQr(Registration $registration): ?string
    {
        if (! $this->hubQris->isConfigured()) {
            return null;
        }

        $reuse = $registration->hub_reference_no
            && $registration->qr_content
            && $registration->payment_status === 'pending'
            && $registration->expired_at
            && $registration->expired_at->isFuture();

        if (! $reuse) {
            $referenceNo = (string) Str::uuid();
            $amount = (int) round((float) $registration->amount);

            try {
                $result = $this->hubQris->generateQr($referenceNo, $amount);
            } catch (Throwable $exception) {
                report($exception);

                return null;
            }

            if (empty($result['qr_content'])) {
                return null;
            }

            $registration->forceFill([
                'hub_reference_no' => $referenceNo,
                'qr_content' => $result['qr_content'],
                'payment_method' => 'qris',
                'payment_status' => 'pending',
                'paid_at' => null,
                'expired_at' => now()->addMinutes((int) config('hubqris.qr_expiry_minutes')),
            ])->save();
        }

        try {
            return (new Builder())->build(data: $registration->qr_content, size: 320, margin: 10)->getDataUri();
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Endpoint AJAX untuk polling status pembayaran dari halaman QR GDC.
     */
    public function status(Registration $registration)
    {
        abort_unless($registration->user_id === auth()->id(), 403);

        if (
            $registration->hub_reference_no
            && $registration->payment_status === 'pending'
            && (! $registration->last_checked_at || $registration->last_checked_at->diffInSeconds(now()) >= 3)
        ) {
            $this->pollHubStatus($registration);
        }

        return response()->json([
            'payment_status' => $registration->payment_status,
            'status' => $registration->status,
        ]);
    }

    private function pollHubStatus(Registration $registration): void
    {
        try {
            $result = $this->hubQris->queryStatus($registration->hub_reference_no);
        } catch (Throwable $exception) {
            report($exception);

            return;
        }

        $registration->last_checked_at = now();

        if (($result['status'] ?? '') === 'paid') {
            $registration->markGdcPaid();

            return;
        }

        if (in_array($result['status'] ?? '', ['failed', 'expired'], true)) {
            $registration->markGdcFailed($result['status']);

            return;
        }

        $registration->save();
    }

    /**
     * Webhook notifikasi dari Midtrans (jalur lama). Route ini dikecualikan
     * dari CSRF.
     */
    public function notification(Request $request, MidtransService $midtrans)
    {
        $midtrans->handleNotification($request->all());

        return response()->json(['message' => 'OK']);
    }
}
