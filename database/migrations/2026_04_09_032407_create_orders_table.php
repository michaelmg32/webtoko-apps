<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
           $table->id();
        $table->string('order_code')->unique();

$table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

$table->string('customer_name')->nullable();
$table->string('customer_phone')->nullable();

$table->foreignId('created_by')->constrained('users');

$table->decimal('total_price', 12, 2)->default(0);

$table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
$table->enum('print_status', ['pending', 'printed'])->default('pending');
$table->enum('pickup_status', ['waiting', 'taken'])->default('waiting');

$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};