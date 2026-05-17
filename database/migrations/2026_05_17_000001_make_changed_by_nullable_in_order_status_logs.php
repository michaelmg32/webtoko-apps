<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_status_logs', function (Blueprint $table) {
            // Make changed_by nullable so we can set it to NULL when deleting a user
            $table->unsignedBigInteger('changed_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_status_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('changed_by')->nullable(false)->change();
        });
    }
};
