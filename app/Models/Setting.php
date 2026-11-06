<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Pengaturan website yang bisa diubah dari halaman admin (tanpa edit kode / .env).
 */
class Setting extends Model
{
    protected $guarded = [];

    protected const CACHE_KEY = 'app_settings';

    /**
     * Key yang disimpan terenkripsi di database (rahasia).
     */
    public const ENCRYPTED_KEYS = [
        'midtrans_server_key',
        'meta_capi_token',
    ];

    /**
     * Ambil semua setting sebagai array [key => value] (sudah didekripsi).
     */
    public static function allValues(): array
    {
        try {
            return static::loadValues();
        } catch (\Throwable $e) {
            // Tabel settings belum dibuat (belum migrate) — pakai default.
            return [];
        }
    }

    protected static function loadValues(): array
    {
        return Cache::rememberForever(static::CACHE_KEY, function () {
            $values = [];

            foreach (static::query()->get(['key', 'value']) as $row) {
                $value = $row->value;

                if ($value !== null && in_array($row->key, static::ENCRYPTED_KEYS, true)) {
                    try {
                        $value = Crypt::decryptString($value);
                    } catch (\Throwable $e) {
                        $value = null;
                    }
                }

                $values[$row->key] = $value;
            }

            return $values;
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::allValues()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function set(string $key, mixed $value): void
    {
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        if ($value !== null && $value !== '' && in_array($key, static::ENCRYPTED_KEYS, true)) {
            $value = Crypt::encryptString((string) $value);
        }

        static::query()->updateOrCreate(['key' => $key], ['value' => $value === '' ? null : $value]);

        Cache::forget(static::CACHE_KEY);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /* ------------------------------------------------------------------
     | Helper khusus
     * ------------------------------------------------------------------ */

    /**
     * Mode pembayaran event berbayar: qris | manual | both
     */
    public static function paymentMode(): string
    {
        $mode = static::get('payment_mode', 'manual');

        return in_array($mode, ['qris', 'manual', 'both'], true) ? $mode : 'manual';
    }

    public static function midtransServerKey(): string
    {
        return (string) static::get('midtrans_server_key', config('services.midtrans.server_key'));
    }

    public static function midtransIsProduction(): bool
    {
        $value = static::get('midtrans_is_production');

        return $value === null
            ? (bool) config('services.midtrans.is_production')
            : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Gambar QRIS statis -- fallback kalau QRIS Dinamis (GDC lewat hub) gagal dibuat.
     */
    public static function qrisStaticImagePath(): ?string
    {
        $path = static::get('qris_static_image_path');

        return $path !== null ? (string) $path : null;
    }

    /**
     * Pixel ID yang aktif (null kalau pixel dimatikan / kosong).
     */
    public static function metaPixelId(): ?string
    {
        if (! static::bool('meta_pixel_enabled')) {
            return null;
        }

        $id = preg_replace('/\D/', '', (string) static::get('meta_pixel_id', ''));

        return $id !== '' ? $id : null;
    }
}
