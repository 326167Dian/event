<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    protected $guarded = [];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    protected static function booted()
    {
        // Hapus file lama jika banner diubah
        static::updating(function ($event) {
            if ($event->isDirty('banner')) {    // jika kolom banner berubah
                $oldBanner = $event->getOriginal('banner');
                if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                    Storage::disk('public')->delete($oldBanner);
                }
            }
        });

        // Hapus file jika event dihapus
        static::deleting(function ($event) {
            if ($event->banner && Storage::disk('public')->exists($event->banner)) {
                Storage::disk('public')->delete($event->banner);
            }
        });
    }
}
