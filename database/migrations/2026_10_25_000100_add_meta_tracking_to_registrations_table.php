<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'meta_tracking')) {
                // fbp, fbc, IP, user agent saat mendaftar — untuk Meta Conversions API
                $table->json('meta_tracking')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (Schema::hasColumn('registrations', 'meta_tracking')) {
                $table->dropColumn('meta_tracking');
            }
        });
    }
};
