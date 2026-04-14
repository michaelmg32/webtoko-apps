<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\FinancialReportService;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Get period from request (daily, monthly, yearly)
        $period = request('period', 'daily');
        
        // Redirect cashflow to daily (removed ARUS KAS feature)
        if ($period === 'cashflow') {
            return redirect()->route('admin.reports.index', ['period' => 'daily']);
        }
        
        // Get custom date range if provided
        $filterStartDate = request('filter_start_date');
        $filterEndDate = request('filter_end_date');

        // Determine date range based on period or filter
        $startDate = now();
        $endDate = now();
        
        if ($filterStartDate && $filterEndDate) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $filterStartDate)->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $filterEndDate)->endOfDay();
        } else {
            switch($period) {
                case 'monthly':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'yearly':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default: // daily
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
            }
        }
        
        // Calculate total revenue based on payment date and period
        // Using DATE() function to compare dates properly regardless of time component
        $totalRevenue = DB::table('payments')
            ->whereBetween(DB::raw('DATE(payment_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->sum(DB::raw('CAST(amount as DECIMAL(12,2))'));

        // Get revenue breakdown by category based on payment date and period
        // Calculate proportional distribution of payments to categories
        $allPayments = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereBetween(DB::raw('DATE(payments.payment_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->select('payments.id', 'payments.order_id', 'payments.amount', 'payments.payment_date')
            ->get();
        
        $revenueByCategory = collect();
        
        foreach ($allPayments as $payment) {
            // Get all items for this order
            $orderItems = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('order_items.order_id', $payment->order_id)
                ->select('products.category', 
                    DB::raw('(CAST(order_items.price as DECIMAL(12,2)) - CAST(COALESCE(order_items.discount, 0) as DECIMAL(12,2))) * CAST(order_items.quantity as DECIMAL(12,2)) as item_total'))
                ->get();
            
            if ($orderItems->isEmpty()) {
                continue;
            }
            
            // Group by category and sum up the item totals
            $categoryTotals = $orderItems->groupBy('category')
                ->map(fn($items) => $items->sum('item_total'));
            
            $totalOrderValue = $categoryTotals->sum();
            
            // Distribute payment proportionally
            if ($totalOrderValue > 0) {
                foreach ($categoryTotals as $category => $categoryValue) {
                    $proportion = $categoryValue / $totalOrderValue;
                    $allocatedAmount = $payment->amount * $proportion;
                    
                    if ($revenueByCategory->has($category)) {
                        $revenueByCategory[$category] += $allocatedAmount;
                    } else {
                        $revenueByCategory[$category] = $allocatedAmount;
                    }
                }
            }
        }
        
        // Convert to collection with proper format
        $revenueByCategory = $revenueByCategory->map(function($amount, $category) {
            return (object)[
                'category' => $category,
                'total_revenue' => round($amount, 2)
            ];
        })->sortByDesc('total_revenue')->values();

        // Get revenue breakdown by payment method
        $revenueByPaymentMethod = DB::table('payments')
            ->whereBetween(DB::raw('DATE(payment_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->select('payment_method', DB::raw('SUM(CAST(amount as DECIMAL(12,2))) as total_revenue'))
            ->groupBy('payment_method')
            ->orderByDesc('total_revenue')
            ->get();

        // Get all paid/partial orders dengan detail pembayaran
        // Note: filter berdasarkan payment_date, bukan created_at
        $allOrders = Order::with(['items.product', 'payments'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->latest('created_at')
            ->get();
        
        // Flatten: setiap payment menjadi satu baris
        $paidOrders = collect();
        foreach ($allOrders as $order) {
            // Group items by category and calculate total amount per category
            $itemsByCategory = $order->items->groupBy('product.category');
            $categoryTotals = [];
            
            foreach ($itemsByCategory as $category => $items) {
                $total = $items->sum(function($item) {
                    $finalPrice = ($item->price ?? 0) - ($item->discount ?? 0);
                    return $finalPrice * ($item->quantity ?? 1);
                });
                $categoryTotals[$category] = $total;
            }
            
            $totalOrderAmount = array_sum($categoryTotals);
            
            // Jika ada payments, buat satu row per payment per kategori
            if ($order->payments->count() > 0) {
                foreach ($order->payments as $payment) {
                    $paymentDate = \Carbon\Carbon::parse($payment->payment_date);
                    
                    // Filter berdasarkan payment_date, bukan created_at
                    if ($paymentDate->between($startDate, $endDate)) {
                        $paymentMethod = ucfirst($payment->payment_method);
                        $paymentTypeLabel = $this->getPaymentTypeLabel(collect([$payment->payment_type]));
                        
                        // Breakdown payment by category dengan proporsi
                        foreach ($categoryTotals as $category => $categoryTotal) {
                            $proportion = $totalOrderAmount > 0 ? $categoryTotal / $totalOrderAmount : 0;
                            $paymentByCategory = round($payment->amount * $proportion);
                            
                            $paidOrders->push((object)[
                                'payment_date' => $payment->payment_date,
                                'order_code' => $order->order_code,
                                'customer_name' => $order->customer_name,
                                'categories' => $category ?: 'N/A',
                                'payment_type_label' => $paymentTypeLabel,
                                'payment_method_label' => $paymentMethod,
                                'total_paid' => $paymentByCategory
                            ]);
                        }
                    }
                }
            } else {
                // Fallback: jika tidak ada payments records
                $createdDate = $order->created_at;
                if ($createdDate->between($startDate, $endDate)) {
                    $paymentTypeLabel = $this->getPaymentTypeLabel(collect([$order->payment_status === 'partial' ? 'dp' : 'full']));
                    
                    // Breakdown total_price by category dengan proporsi
                    foreach ($categoryTotals as $category => $categoryTotal) {
                        $proportion = $totalOrderAmount > 0 ? $categoryTotal / $totalOrderAmount : 0;
                        $priceByCategory = round($order->total_price * $proportion);
                        
                        $paidOrders->push((object)[
                            'payment_date' => $order->created_at,
                            'order_code' => $order->order_code,
                            'customer_name' => $order->customer_name,
                            'categories' => $category ?: 'N/A',
                            'payment_type_label' => $paymentTypeLabel,
                            'payment_method_label' => 'Cash',
                            'total_paid' => $priceByCategory
                        ]);
                    }
                }
            }
        }
        
        // Sort by payment date descending
        $paidOrders = $paidOrders->sortByDesc('payment_date')->values();

        // Apply category filter if provided
        $filterCategory = request('filter_category');
        if ($filterCategory) {
            $paidOrders = $paidOrders->filter(function($order) use ($filterCategory) {
                return $order->categories === $filterCategory;
            });
        }

        // Apply payment method filter if provided
        $filterPaymentMethod = request('filter_payment_method');
        if ($filterPaymentMethod) {
            $paidOrders = $paidOrders->filter(function($order) use ($filterPaymentMethod) {
                // Check if payment method is in the combined string (e.g., "Cash", "Transfer", "Cash+Transfer")
                return strpos(strtolower($order->payment_method_label), strtolower($filterPaymentMethod)) !== false;
            });
        }

        $recentOrders = $paidOrders;

        return view('admin.reports', [
            'totalRevenue' => $totalRevenue,
            'revenueByCategory' => $revenueByCategory,
            'revenueByPaymentMethod' => $revenueByPaymentMethod,
            'recentOrders' => $recentOrders,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Handle cashflow report
     */
    private function cashflowReport()
    {
        // Get date range for cashflow
        $cashflowStartDate = request('cashflow_start_date');
        $cashflowEndDate = request('cashflow_end_date');

        $startDate = $cashflowStartDate 
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $cashflowStartDate)->startOfDay()
            : now()->subDays(7)->startOfDay();
        
        $endDate = $cashflowEndDate 
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $cashflowEndDate)->endOfDay()
            : now()->endOfDay();

        // Get cashflow report using service
        $reportService = new FinancialReportService();
        $cashflowReport = $reportService->getDailyReport($startDate, $endDate);

        // Default data untuk period yang lain
        $totalRevenue = 0;
        $revenueByCategory = collect([]);
        $recentOrders = collect([]);

        return view('admin.reports', [
            'totalRevenue' => $totalRevenue,
            'revenueByCategory' => $revenueByCategory,
            'recentOrders' => $recentOrders,
            'cashflowReport' => $cashflowReport,
            'period' => 'cashflow',
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function export()
    {
        // Get period from request (daily, monthly, yearly)
        $period = request('period', 'daily');
        
        // Get custom date range if provided
        $filterStartDate = request('filter_start_date');
        $filterEndDate = request('filter_end_date');

        // Determine date range based on period or filter
        $startDate = now();
        $endDate = now();
        
        if ($filterStartDate && $filterEndDate) {
            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $filterStartDate)->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $filterEndDate)->endOfDay();
        } else {
            switch($period) {
                case 'monthly':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'yearly':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default: // daily
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
            }
        }

        // Get all paid/partial orders with payment details
        // Note: filter berdasarkan payment_date, bukan created_at
        $allOrders = Order::with(['items.product', 'payments'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->latest('created_at')
            ->get();
        
        // Flatten: setiap payment menjadi satu baris per kategori
        $orders = collect();
        foreach ($allOrders as $order) {
            // Group items by category and calculate total amount per category
            $itemsByCategory = $order->items->groupBy('product.category');
            $categoryTotals = [];
            
            foreach ($itemsByCategory as $category => $items) {
                $total = $items->sum(function($item) {
                    $finalPrice = ($item->price ?? 0) - ($item->discount ?? 0);
                    return $finalPrice * ($item->quantity ?? 1);
                });
                $categoryTotals[$category] = $total;
            }
            
            $totalOrderAmount = array_sum($categoryTotals);
            
            // Jika ada payments, buat satu row per payment per kategori
            if ($order->payments->count() > 0) {
                foreach ($order->payments as $payment) {
                    $paymentDate = \Carbon\Carbon::parse($payment->payment_date);
                    
                    // Filter berdasarkan payment_date, bukan created_at
                    if ($paymentDate->between($startDate, $endDate)) {
                        $paymentMethod = ucfirst($payment->payment_method);
                        $paymentTypeLabel = $this->getPaymentTypeLabel(collect([$payment->payment_type]));
                        
                        // Breakdown payment by category dengan proporsi
                        foreach ($categoryTotals as $category => $categoryTotal) {
                            $proportion = $totalOrderAmount > 0 ? $categoryTotal / $totalOrderAmount : 0;
                            $paymentByCategory = round($payment->amount * $proportion);
                            
                            $orders->push((object)[
                                'payment_date' => $payment->payment_date,
                                'order_code' => $order->order_code,
                                'customer_name' => $order->customer_name,
                                'categories' => $category ?: 'N/A',
                                'payment_type_label' => $paymentTypeLabel,
                                'payment_method_label' => $paymentMethod,
                                'total_paid' => $paymentByCategory
                            ]);
                        }
                    }
                }
            } else {
                // Fallback: jika tidak ada payments records
                $createdDate = $order->created_at;
                if ($createdDate->between($startDate, $endDate)) {
                    $paymentTypeLabel = $this->getPaymentTypeLabel(collect([$order->payment_status === 'partial' ? 'dp' : 'full']));
                    
                    // Breakdown total_price by category dengan proporsi
                    foreach ($categoryTotals as $category => $categoryTotal) {
                        $proportion = $totalOrderAmount > 0 ? $categoryTotal / $totalOrderAmount : 0;
                        $priceByCategory = round($order->total_price * $proportion);
                        
                        $orders->push((object)[
                            'payment_date' => $order->created_at,
                            'order_code' => $order->order_code,
                            'customer_name' => $order->customer_name,
                            'categories' => $category ?: 'N/A',
                            'payment_type_label' => $paymentTypeLabel,
                            'payment_method_label' => 'Cash',
                            'total_paid' => $priceByCategory
                        ]);
                    }
                }
            }
        }
        
        // Sort by payment date descending
        $orders = $orders->sortByDesc('payment_date')->values();

        // Apply category filter if provided
        $filterCategory = request('filter_category');
        if ($filterCategory) {
            $orders = $orders->filter(function($order) use ($filterCategory) {
                return $order->categories === $filterCategory;
            });
        }

        // Apply payment method filter if provided
        $filterPaymentMethod = request('filter_payment_method');
        if ($filterPaymentMethod) {
            $orders = $orders->filter(function($order) use ($filterPaymentMethod) {
                // Check if payment method is in the combined string (e.g., "Cash", "Transfer", "Cash+Transfer")
                return strpos(strtolower($order->payment_method_label), strtolower($filterPaymentMethod)) !== false;
            });
        }

        // Calculate totals by payment type
        $dpTotal = 0;
        $fullPaymentTotal = 0;
        $pelunasanTotal = 0;

        foreach($orders as $order) {
            if(strpos($order->payment_type_label, 'DP') !== false) {
                $dpTotal += $order->total_paid;
            } elseif(strpos($order->payment_type_label, 'Bayar Penuh') !== false) {
                $fullPaymentTotal += $order->total_paid;
            } elseif(strpos($order->payment_type_label, 'Pelunasan') !== false) {
                $pelunasanTotal += $order->total_paid;
            }
        }

        $grandTotal = $dpTotal + $fullPaymentTotal + $pelunasanTotal;

        // Create CSV data
        $csv = "LAPORAN ORDER YANG SUDAH DIBAYAR\n";
        $csv .= "Periode: " . $startDate->format('d/m/Y') . " - " . $endDate->format('d/m/Y') . "\n\n";
        
        // Summary section
        $csv .= "RINGKASAN\n";
        $csv .= "Tipe Pembayaran,Jumlah\n";
        $csv .= "\"DP\",\"" . number_format($dpTotal, 0, '.', '') . "\"\n";
        $csv .= "\"Bayar Penuh\",\"" . number_format($fullPaymentTotal, 0, '.', '') . "\"\n";
        $csv .= "\"Pelunasan\",\"" . number_format($pelunasanTotal, 0, '.', '') . "\"\n";
        $csv .= "\"TOTAL\",\"" . number_format($grandTotal, 0, '.', '') . "\"\n\n";

        // Detail section
        $csv .= "DETAIL ORDER\n";
        $csv .= "No,Tanggal Pembayaran,Nomor Order,Nama Pelanggan,Kategori,Tipe Pembayaran,Metode Pembayaran,Jumlah (Rp)\n";

        // Data rows
        foreach ($orders as $index => $order) {
            $no = $index + 1;
            $tanggal = \Carbon\Carbon::parse($order->payment_date)->format('d/m/Y H:i');
            $orderCode = $order->order_code;
            $customerName = $order->customer_name ?? 'N/A';
            $categories = $order->categories ?? 'N/A';
            $paymentType = $order->payment_type_label;
            $paymentMethod = $order->payment_method_label;
            $totalPrice = number_format($order->total_paid, 0, '.', '');
            
            $csv .= "$no,\"$tanggal\",\"$orderCode\",\"$customerName\",\"$categories\",\"$paymentType\",\"$paymentMethod\",$totalPrice\n";
        }

        // Download file
        $filename = 'Laporan_Order_Dibayar_' . $startDate->format('d-m-Y') . '_' . $endDate->format('d-m-Y') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        echo "\xEF\xBB\xBF"; // BOM for UTF-8
        echo $csv;
        exit;
    }

    /**
     * Get payment type label from array of payment types
     */
    private function getPaymentTypeLabel($paymentTypes)
    {
        if($paymentTypes->isEmpty()) {
            return 'N/A';
        }

        $types = $paymentTypes->map(function($type) {
            switch($type) {
                case 'dp':
                    return 'DP';
                case 'full':
                    return 'Bayar Penuh';
                case 'pelunasan':
                    return 'Pelunasan';
                default:
                    return ucfirst($type);
            }
        })->unique();

        return $types->join(', ');
    }

    /**
     * Get payment method label
     */
    private function getPaymentMethodLabel($paymentMethod)
    {
        switch($paymentMethod) {
            case 'cash':
                return 'Cash';
            case 'transfer':
                return 'Transfer';
            case 'qris':
                return 'QRIS';
            default:
                return ucfirst($paymentMethod);
        }
    }

    public function income()
    {
        $orders = Order::where('payment_status', 'paid')->get();

        $total = $orders->sum('total_price');

        return response()->json([
            'total_income' => $total,
            'orders' => $orders
        ]);
    }
}