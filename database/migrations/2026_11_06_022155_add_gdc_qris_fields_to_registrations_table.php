<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'hub_reference_no')) {
                $table->string('hub_reference_no')->nullable()->unique()->after('midtrans_response');
            }
            if (! Schema::hasColumn('registrations', 'qr_content')) {
                $table->text('qr_content')->nullable()->after('hub_reference_no');
            }
            if (! Schema::hasColumn('registrations', 'last_checked_at')) {
                $table->timestamp('last_checked_at')->nullable()->after('qr_content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['hub_reference_no', 'qr_content', 'last_checked_at']);
        });
    }
};
