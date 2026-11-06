<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

/**
 * Menerima notifikasi yang DITERUSKAN hub (mysifacenter.my.id) -- situs ini
 * bukan partner GDC terdaftar sendiri (GDC cuma izinkan 1 url callback per
 * partner), lihat HubQrisService.
 */
class HubQrisWebhookController extends Controller
{
    public function notification(Request $request)
    {
        $key = (string) $request->header('X-Branch-Key', '');

        abort_unless($key !== '' && hash_equals((string) config('hubqris.api_key'), $key), 401);

        $registration = Registration::where('hub_reference_no', (string) $request->input('reference_no'))->first();

        abort_unless($registration, 404);

        $status = (string) $request->input('status');

        if ($status === 'paid') {
            $registration->markGdcPaid();
        } elseif (in_array($status, ['failed', 'expired'], true)) {
            $registration->markGdcFailed($status);
        }

        return response()->json(['status' => 'ok']);
    }
}
