<?php

namespace App\Services;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialReportService
{
    /**
     * Generate laporan keuangan harian dengan detail DP
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getDailyReport($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth();
        }
        if (!$endDate) {
            $endDate = Carbon::now()->endOfDay();
        }

        // Get semua payment dalam range tanggal
        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->with('order')
            ->get()
            ->groupBy(fn($payment) => Carbon::parse($payment->payment_date)->format('Y-m-d'));

        $report = [];
        $totalDP = 0;
        $totalPelunasan = 0;
        $totalFull = 0;
        $grandTotal = 0;

        // Loop per hari
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dayPayments = $payments[$dateKey] ?? collect();

            $dpAmount = $dayPayments->where('payment_type', 'dp')->sum('amount');
            $pelunasanAmount = $dayPayments->where('payment_type', 'pelunasan')->sum('amount');
            $fullAmount = $dayPayments->where('payment_type', 'full')->sum('amount');
            $dailyTotal = $dpAmount + $pelunasanAmount + $fullAmount;

            if ($dailyTotal > 0 || $dayPayments->isNotEmpty()) {
                $report[] = [
                    'date' => $dateKey,
                    'date_formatted' => Carbon::parse($dateKey)->format('d M Y (l)'),
                    'dp_amount' => $dpAmount,
                    'pelunasan_amount' => $pelunasanAmount,
                    'full_payment_amount' => $fullAmount,
                    'daily_total' => $dailyTotal,
                    'transaction_count' => $dayPayments->count(),
                    'details' => $dayPayments->values()->toArray()
                ];

                $totalDP += $dpAmount;
                $totalPelunasan += $pelunasanAmount;
                $totalFull += $fullAmount;
                $grandTotal += $dailyTotal;
            }

            $currentDate->addDay();
        }

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'start_formatted' => $startDate->format('d M Y'),
                'end_formatted' => $endDate->format('d M Y'),
                'total_days' => $endDate->diffInDays($startDate) + 1
            ],
            'daily_details' => $report,
            'summary' => [
                'total_dp_in' => $totalDP,
                'total_pelunasan_in' => $totalPelunasan,
                'total_full_payment_in' => $totalFull,
                'total_cashflow' => $grandTotal,
                'total_transactions' => Payment::whereBetween('payment_date', [$startDate, $endDate])->count(),
                'avg_daily_cashflow' => !empty($report) ? round($grandTotal / count($report), 2) : 0
            ]
        ];
    }

    /**
     * Generate laporan keuangan per metode pembayaran
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getPaymentMethodReport($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth();
        }
        if (!$endDate) {
            $endDate = Carbon::now()->endOfDay();
        }

        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->get()
            ->groupBy('payment_method');

        $report = [];
        $grandTotal = 0;

        foreach ($payments as $method => $methodPayments) {
            $dpAmount = $methodPayments->where('payment_type', 'dp')->sum('amount');
            $pelunasanAmount = $methodPayments->where('payment_type', 'pelunasan')->sum('amount');
            $fullAmount = $methodPayments->where('payment_type', 'full')->sum('amount');
            $methodTotal = $dpAmount + $pelunasanAmount + $fullAmount;

            $report[] = [
                'payment_method' => $method,
                'dp_amount' => $dpAmount,
                'pelunasan_amount' => $pelunasanAmount,
                'full_payment_amount' => $fullAmount,
                'method_total' => $methodTotal,
                'transaction_count' => $methodPayments->count(),
                'percentage' => 0 // Akan di-calculate di bawah
            ];

            $grandTotal += $methodTotal;
        }

        // Calculate percentage
        foreach ($report as &$item) {
            $item['percentage'] = $grandTotal > 0 ? round(($item['method_total'] / $grandTotal) * 100, 2) : 0;
        }

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'start_formatted' => $startDate->format('d M Y'),
                'end_formatted' => $endDate->format('d M Y')
            ],
            'by_method' => $report,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * Generate laporan DP yang belum dilunasi
     * @return array
     */
    public function getOutstandingDPReport()
    {
        $orders = \App\Models\Order::where('dp_status', 'partial_dp')
            ->orWhere('payment_status', 'partial')
            ->with(['payments', 'customer'])
            ->get();

        $report = [];
        $totalOutstanding = 0;

        foreach ($orders as $order) {
            $payments = Payment::where('order_id', $order->id)->get();
            $totalPaid = $payments->sum('amount');
            $outstanding = $order->total_price - $totalPaid;

            if ($outstanding > 0) {
                $report[] = [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->customer_name,
                    'total_price' => $order->total_price,
                    'dp_amount' => $order->dp_amount,
                    'total_paid' => $totalPaid,
                    'outstanding_amount' => $outstanding,
                    'outstanding_percentage' => round(($outstanding / $order->total_price) * 100, 2),
                    'created_at' => $order->created_at->format('d M Y'),
                    'days_outstanding' => $order->created_at->diffInDays(now())
                ];

                $totalOutstanding += $outstanding;
            }
        }

        // Sort by days outstanding (paling lama di atas)
        usort($report, function($a, $b) {
            return $b['days_outstanding'] <=> $a['days_outstanding'];
        });

        return [
            'outstanding_orders' => $report,
            'summary' => [
                'total_outstanding_orders' => count($report),
                'total_outstanding_amount' => $totalOutstanding,
                'avg_outstanding_amount' => !empty($report) ? round($totalOutstanding / count($report), 2) : 0
            ]
        ];
    }

    /**
     * Generate laporan perbandingan DP vs Pelunasan
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getDPvsPelunasanReport($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth();
        }
        if (!$endDate) {
            $endDate = Carbon::now()->endOfDay();
        }

        $dpPayments = Payment::where('payment_type', 'dp')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $pelunasanPayments = Payment::whereIn('payment_type', ['pelunasan', 'full'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $totalDP = $dpPayments->sum('amount');
        $totalPelunasan = $pelunasanPayments->sum('amount');
        $grandTotal = $totalDP + $totalPelunasan;

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'start_formatted' => $startDate->format('d M Y'),
                'end_formatted' => $endDate->format('d M Y')
            ],
            'comparison' => [
                'total_dp' => $totalDP,
                'total_pelunasan' => $totalPelunasan,
                'grand_total' => $grandTotal,
                'dp_percentage' => $grandTotal > 0 ? round(($totalDP / $grandTotal) * 100, 2) : 0,
                'pelunasan_percentage' => $grandTotal > 0 ? round(($totalPelunasan / $grandTotal) * 100, 2) : 0,
                'dp_transaction_count' => $dpPayments->count(),
                'pelunasan_transaction_count' => $pelunasanPayments->count()
            ]
        ];
    }
}
