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
            if (! Schema::hasColumn('registrations', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount');
            }
            if (! Schema::hasColumn('registrations', 'order_id')) {
                $table->string('order_id')->nullable()->unique()->after('payment_method');
            }
            if (! Schema::hasColumn('registrations', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('order_id');
            }
            if (! Schema::hasColumn('registrations', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('transaction_id');
            }
            if (! Schema::hasColumn('registrations', 'qr_url')) {
                $table->string('qr_url')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('registrations', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('qr_url');
            }
            if (! Schema::hasColumn('registrations', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('paid_at');
            }
            if (! Schema::hasColumn('registrations', 'midtrans_response')) {
                $table->json('midtrans_response')->nullable()->after('expired_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'order_id',
                'transaction_id',
                'payment_status',
                'qr_url',
                'paid_at',
                'expired_at',
                'midtrans_response',
            ]);
        });
    }
};
