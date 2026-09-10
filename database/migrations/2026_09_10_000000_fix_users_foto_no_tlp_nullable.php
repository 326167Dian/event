<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * `no_tlp`/`foto` di users dan `foto` di registrations ternyata NOT NULL
     * di database walau migration aslinya nullable, sehingga insert user/registrasi
     * baru selalu gagal saat foto tidak diisi. doctrine/dbal tidak terpasang,
     * jadi pakai raw ALTER TABLE.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE users MODIFY no_tlp TEXT NULL');
        DB::statement('ALTER TABLE users MODIFY foto TEXT NULL');
        DB::statement('ALTER TABLE registrations MODIFY foto TEXT NULL');
    }

    public function down(): void
    {
        // Sengaja tidak dikembalikan ke NOT NULL, karena baris yang sudah
        // memiliki nilai NULL akan membuat rollback ini gagal/merusak data.
    }
};
