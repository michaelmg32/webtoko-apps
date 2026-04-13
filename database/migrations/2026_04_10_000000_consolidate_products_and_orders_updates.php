<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add product fields: amatir_price and stock
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('amatir_price', 12, 2)->default(0)->after('price');
            $table->integer('stock')->default(0)->after('amatir_price');
            $table->boolean('unlimited_stock')->default(false)->after('stock');
        });

        // Add order fields: discount and update print_status enum
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('discount_percentage')->default(0)->after('total_price')->comment('Discount percentage 0-100');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_percentage')->comment('Discount amount in currency');
        });

        // For MySQL, update the print_status enum
        DB::statement("ALTER TABLE orders MODIFY print_status ENUM('pending', 'printed', 'not_needed') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert print_status enum
        DB::statement("ALTER TABLE orders MODIFY print_status ENUM('pending', 'printed') DEFAULT 'pending'");

        // Drop order discount columns
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'discount_amount']);
        });

        // Drop product columns
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['amatir_price', 'stock', 'unlimited_stock']);
        });
    }
};
