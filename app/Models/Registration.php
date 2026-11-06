<?php

namespace App\Models;

use App\Services\MetaConversionsService;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'midtrans_response' => 'array',
        'meta_tracking' => 'array',
    ];

    protected static function booted()
    {
        // Saat admin meng-approve event berbayar (mis. transfer manual), catat waktu lunas.
        static::saving(function (Registration $registration) {
            if (
                $registration->isDirty('status')
                && $registration->status === 'approved'
                && (float) $registration->amount > 0
                && $registration->paid_at === null
            ) {
                $registration->paid_at = now();
            }
        });

        // Begitu pertama kali lunas (QRIS via webhook / manual via approve admin),
        // kirim event Purchase ke Meta Conversions API (kalau diaktifkan di Pengaturan).
        static::saved(function (Registration $registration) {
            if (
                $registration->paid_at !== null
                && $registration->getOriginal('paid_at') === null
            ) {
                // Dikirim setelah response selesai agar tidak memperlambat webhook / halaman admin.
                app()->terminating(fn () => app(MetaConversionsService::class)->sendPurchase($registration));
            }
        });
    }

    /**
     * ID unik transaksi untuk pelacakan Meta (sama di browser & server agar tidak dobel).
     */
    public function getTrackingEventIdAttribute(): string
    {
        return $this->order_id ?: 'REG-' . $this->id;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Tandai lunas lewat QRIS Dinamis (GDC via hub) -- dipanggil dari polling
     * browser maupun webhook hub, keduanya bisa balapan, jadi harus idempoten.
     */
    public function markGdcPaid(): void
    {
        \DB::transaction(function () {
            $this->refresh();

            if ($this->status === 'approved') {
                return;
            }

            $this->status = 'approved';
            $this->payment_method = 'qris';
            $this->payment_status = 'capture';
            $this->paid_at ??= now();
            $this->save();
        });
    }

    public function markGdcFailed(string $status): void
    {
        if ($this->status === 'approved') {
            return;
        }

        $this->payment_status = in_array($status, ['failed', 'expired'], true) ? 'expire' : $status;
        $this->save();
    }


    // public function approve(string $adminNote = null)
    // {
    //     $this->update(['status' => 'approved', 'admin_note' => $adminNote]);
    //     // trigger notification / email
    //     $this->user->notify(new \App\Notifications\RegistrationApproved($this));
    // }


    // public function reject(string $reason = null)
    // {
    //     $this->update(['status' => 'rejected', 'admin_note' => $reason]);
    //     $this->user->notify(new \App\Notifications\RegistrationRejected($this));
    // }
}
