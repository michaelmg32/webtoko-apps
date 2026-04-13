<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use Exception;

class PaymentService
{
    /**
     * Proses pembayaran DP
     * @param int $orderId
     * @param float $dpAmount
     * @param int $paidBy
     * @param string $paymentMethod
     * @return Payment
     */
    public function recordDPPayment($orderId, $dpAmount, $paidBy, $paymentMethod = 'cash')
    {
        $order = Order::findOrFail($orderId);
        
        // Validasi jumlah DP
        $totalPaid = $order->dp_amount + $dpAmount;
        if ($totalPaid > $order->total_price) {
            throw new Exception('Jumlah DP tidak boleh melebihi total harga pesanan');
        }

        // Hitung sisa pembayaran setelah DP
        $remainingAmount = $order->total_price - $totalPaid;

        // Buat record pembayaran DP
        $payment = Payment::create([
            'order_id' => $orderId,
            'paid_by' => $paidBy,
            'amount' => $dpAmount,
            'payment_method' => $paymentMethod,
            'payment_date' => now(),
            'payment_type' => 'dp',
            'remaining_amount' => $remainingAmount,
            'dp_reference' => 'DP-' . $order->order_code . '-' . now()->format('Ymd')
        ]);

        // Update order DP tracking
        $order->dp_amount += $dpAmount;
        
        // Tentukan dp_status
        if ($order->dp_amount >= $order->total_price) {
            $order->dp_status = 'full_dp';
            $order->payment_status = 'paid';
        } elseif ($order->dp_amount > 0) {
            $order->dp_status = 'partial_dp';
            $order->payment_status = 'partial';
        } else {
            $order->dp_status = 'no_dp';
        }

        $order->save();

        return $payment;
    }

    /**
     * Proses pembayaran pelunasan
     * @param int $orderId
     * @param float $pelunasanAmount
     * @param int $paidBy
     * @param string $paymentMethod
     * @return Payment
     */
    public function recordPelunasanPayment($orderId, $pelunasanAmount, $paidBy, $paymentMethod = 'cash')
    {
        $order = Order::findOrFail($orderId);
        
        // Hitung total yang sudah terbayar sebelumnya
        $previousPayments = Payment::where('order_id', $orderId)->sum('amount');
        $remainingToPay = $order->total_price - $previousPayments;

        // Validasi jumlah pelunasan
        if ($pelunasanAmount > $remainingToPay) {
            throw new Exception('Jumlah pelunasan melebihi sisa pembayaran. Sisa: ' . $remainingToPay);
        }

        // Hitung sisa setelah pelunasan
        $newRemaining = $remainingToPay - $pelunasanAmount;

        // Buat record pembayaran
        $payment = Payment::create([
            'order_id' => $orderId,
            'paid_by' => $paidBy,
            'amount' => $pelunasanAmount,
            'payment_method' => $paymentMethod,
            'payment_date' => now(),
            'payment_type' => 'pelunasan',
            'remaining_amount' => $newRemaining
        ]);

        // Update order payment status
        if ($newRemaining <= 0) {
            $order->payment_status = 'paid';
        } else {
            $order->payment_status = 'partial';
        }
        $order->save();

        return $payment;
    }

    /**
     * Proses pembayaran penuh
     * @param int $orderId
     * @param int $paidBy
     * @param string $paymentMethod
     * @return Payment
     */
    public function recordFullPayment($orderId, $paidBy, $paymentMethod = 'cash')
    {
        $order = Order::findOrFail($orderId);
        
        // Check apakah sudah ada pembayaran sebelumnya
        $previousPayments = Payment::where('order_id', $orderId)->sum('amount');
        $totalToPay = $order->total_price - $previousPayments;

        if ($totalToPay <= 0) {
            throw new Exception('Pesanan ini sudah lunas');
        }

        // Buat record pembayaran penuh
        $payment = Payment::create([
            'order_id' => $orderId,
            'paid_by' => $paidBy,
            'amount' => $totalToPay,
            'payment_method' => $paymentMethod,
            'payment_date' => now(),
            'payment_type' => 'full',
            'remaining_amount' => 0
        ]);

        // Update order payment status
        $order->payment_status = 'paid';
        if (!$order->dp_amount > 0) {
            $order->dp_status = 'no_dp';
        }
        $order->save();

        return $payment;
    }

    /**
     * Get sisa pembayaran untuk order tertentu
     * @param int $orderId
     * @return float
     */
    public function getRemainingPayment($orderId)
    {
        $order = Order::findOrFail($orderId);
        $totalPaid = Payment::where('order_id', $orderId)->sum('amount');
        return $order->total_price - $totalPaid;
    }

    /**
     * Get detail pembayaran untuk order
     * @param int $orderId
     * @return array
     */
    public function getOrderPaymentDetail($orderId)
    {
        $order = Order::findOrFail($orderId);
        $payments = Payment::where('order_id', $orderId)
            ->orderBy('payment_date', 'asc')
            ->get();

        $dpAmount = $payments->where('payment_type', 'dp')->sum('amount');
        $pelunasanAmount = $payments->where('payment_type', 'pelunasan')->sum('amount');
        $fullPaymentAmount = $payments->where('payment_type', 'full')->sum('amount');
        $totalPaid = $dpAmount + $pelunasanAmount + $fullPaymentAmount;
        $remaining = $order->total_price - $totalPaid;

        return [
            'order_id' => $orderId,
            'order_code' => $order->order_code,
            'total_price' => $order->total_price,
            'dp_amount' => $dpAmount,
            'pelunasan_amount' => $pelunasanAmount,
            'full_payment_amount' => $fullPaymentAmount,
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'is_paid' => $remaining <= 0,
            'payments' => $payments
        ];
    }
}
