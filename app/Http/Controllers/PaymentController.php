<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\PaymentService;
use App\Services\FinancialReportService;
use Exception;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,transfer'
        ]);

        $order = Order::findOrFail($request->order_id);

        // Check if order already paid
        if ($order->payment_status === 'paid') {
            return redirect()->back()->withErrors(['error' => 'Order sudah dibayar!']);
        }

        Payment::create([
            'order_id' => $order->id,
            'paid_by' => auth()->id(),
            'amount' => $request->amount,
            'payment_method' => $request->method,
            'payment_date' => now()
        ]);

        // Update order status
        $order->update([
            'payment_status' => 'paid'
        ]);

        // Log status change
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status_type' => 'payment',
            'old_status' => 'unpaid',
            'new_status' => 'paid',
            'changed_by' => auth()->id()
        ]);

        $methodLabel = match($request->method) {
            'cash' => 'Cash',
            'transfer' => 'Transfer',
            default => ucfirst($request->method)
        };

        return redirect()->route('kasir.orders.index')->with('success', 'Payment berhasil! Order ' . $order->order_code . ' dibayar via ' . $methodLabel);
    }

    /**
     * Record pembayaran DP
     */
    public function recordDP(Request $request)
    {
        try {
            $paymentService = new PaymentService();

            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'dp_amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:cash,transfer,qris'
            ]);

            $payment = $paymentService->recordDPPayment(
                $validated['order_id'],
                $validated['dp_amount'],
                auth()->id(),
                $validated['payment_method']
            );

            // Log status change
            OrderStatusLog::create([
                'order_id' => $validated['order_id'],
                'status_type' => 'payment',
                'old_status' => 'unpaid',
                'new_status' => 'partial',
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'DP pembayaran berhasil dicatat',
                'payment' => $payment,
                'order' => Order::find($validated['order_id'])
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Record pembayaran pelunasan/lanjutan
     */
    public function recordPelunasan(Request $request)
    {
        try {
            $paymentService = new PaymentService();

            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:cash,transfer,qris'
            ]);

            $payment = $paymentService->recordPelunasanPayment(
                $validated['order_id'],
                $validated['amount'],
                auth()->id(),
                $validated['payment_method']
            );

            $order = Order::find($validated['order_id']);

            // Log status change
            OrderStatusLog::create([
                'order_id' => $validated['order_id'],
                'status_type' => 'payment',
                'old_status' => $order->payment_status,
                'new_status' => $order->payment_status === 'paid' ? 'paid' : 'partial',
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pelunasan berhasil dicatat',
                'payment' => $payment,
                'order' => $order->fresh()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get detail pembayaran order
     */
    public function getPaymentDetail($orderId)
    {
        try {
            $paymentService = new PaymentService();
            $detail = $paymentService->getOrderPaymentDetail($orderId);

            return response()->json([
                'success' => true,
                'data' => $detail
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Laporan keuangan harian dengan detail DP
     */
    public function dailyReport(Request $request)
    {
        $reportService = new FinancialReportService();

        $startDate = $request->input('start_date') ? 
            \Carbon\Carbon::parse($request->input('start_date'))->startOfDay() : 
            \Carbon\Carbon::now()->startOfMonth();
        
        $endDate = $request->input('end_date') ? 
            \Carbon\Carbon::parse($request->input('end_date'))->endOfDay() : 
            \Carbon\Carbon::now()->endOfDay();

        $report = $reportService->getDailyReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($report);
        }

        return view('admin.reports.financial-daily', ['report' => $report]);
    }

    /**
     * Laporan per metode pembayaran
     */
    public function paymentMethodReport(Request $request)
    {
        $reportService = new FinancialReportService();

        $startDate = $request->input('start_date') ? 
            \Carbon\Carbon::parse($request->input('start_date'))->startOfDay() : 
            \Carbon\Carbon::now()->startOfMonth();
        
        $endDate = $request->input('end_date') ? 
            \Carbon\Carbon::parse($request->input('end_date'))->endOfDay() : 
            \Carbon\Carbon::now()->endOfDay();

        $report = $reportService->getPaymentMethodReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($report);
        }

        return view('payment.method-report', ['report' => $report]);
    }

    /**
     * Laporan DP yang belum dilunasi
     */
    public function outstandingDPReport()
    {
        $reportService = new FinancialReportService();
        $report = $reportService->getOutstandingDPReport();

        return response()->json($report);
    }

    /**
     * Laporan perbandingan DP vs Pelunasan
     */
    public function dpvsPelunasanReport(Request $request)
    {
        $reportService = new FinancialReportService();

        $startDate = $request->input('start_date') ? 
            \Carbon\Carbon::parse($request->input('start_date'))->startOfDay() : 
            \Carbon\Carbon::now()->startOfMonth();
        
        $endDate = $request->input('end_date') ? 
            \Carbon\Carbon::parse($request->input('end_date'))->endOfDay() : 
            \Carbon\Carbon::now()->endOfDay();

        $report = $reportService->getDPvsPelunasanReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($report);
        }

        return view('payment.dp-vs-pelunasan', ['report' => $report]);
    }
}