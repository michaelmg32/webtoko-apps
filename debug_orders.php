<?php
// Quick debug script
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\DB;

echo "=== ORDERS WITH paid OR partial STATUS ===\n";
$orders = DB::table('orders')
    ->whereIn('payment_status', ['paid', 'partial'])
    ->where('customer_name', 'budi')
    ->get();

foreach($orders as $order) {
    echo "Order ID: {$order->id}, Code: {$order->order_code}, Status: {$order->payment_status}\n";
    
    echo "  Payments for this order:\n";
    $payments = DB::table('payments')
        ->where('order_id', $order->id)
        ->get();
    
    if($payments->count() > 0) {
        foreach($payments as $payment) {
            echo "    - Amount: {$payment->amount}, Type: {$payment->payment_type}, Date: {$payment->payment_date}\n";
        }
    } else {
        echo "    - NO PAYMENTS FOUND\n";
    }
}

echo "\n=== ALL ORDERS ===\n";
$allOrders = DB::table('orders')
    ->where('customer_name', 'budi')
    ->get();

foreach($allOrders as $order) {
    echo "Order ID: {$order->id}, Code: {$order->order_code}, Status: {$order->payment_status}, Total: {$order->total_price}\n";
}
