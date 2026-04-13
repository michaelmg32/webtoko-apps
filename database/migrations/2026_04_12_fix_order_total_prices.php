<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Recalculate total_price for all orders using per-item subtotals
        // This fixes the double-discount bug where discount_amount was subtracted twice
        foreach (Order::all() as $order) {
            if ($order->items && count($order->items) > 0) {
                $total = 0;
                foreach ($order->items as $item) {
                    // Calculate subtotal based on per-item discount
                    $finalPrice = $item->price - ($item->discount ?? 0);
                    $itemSubtotal = $finalPrice * $item->quantity;
                    $total += $itemSubtotal;
                }
                // Update order with correct total (no further discount subtraction)
                $order->update(['total_price' => max(0, $total)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No automatic rollback - manual intervention would be needed
    }
};
