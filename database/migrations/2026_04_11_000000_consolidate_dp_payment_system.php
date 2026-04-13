<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Consolidated DP Payment System Migration
     * Adds all required columns and enums for Down Payment (DP) functionality
     */
    public function up(): void
    {
        // 1. Update orders table payment_status enum to include 'partial'
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])
                ->default('unpaid')
                ->change();
        });

        // 2. Add DP tracking columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('dp_amount', 12, 2)->default(0)->after('total_price');
            $table->enum('dp_status', ['no_dp', 'partial_dp', 'full_dp'])->default('no_dp')->after('dp_amount');
        });

        // 3. Add payment type and related columns to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_type', ['dp', 'full', 'pelunasan'])->default('full')->after('payment_method');
            $table->decimal('remaining_amount', 12, 2)->nullable()->after('payment_type');
            $table->string('dp_reference')->nullable()->after('remaining_amount');
        });
    }

    public function down(): void
    {
        // Reverse order: payments table first, then orders table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'remaining_amount', 'dp_reference']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['dp_amount', 'dp_status']);
            // Revert payment_status enum to original
            $table->enum('payment_status', ['unpaid', 'paid'])
                ->default('unpaid')
                ->change();
        });
    }
};
