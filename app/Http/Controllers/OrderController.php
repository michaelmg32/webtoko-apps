<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('created_by', auth()->id())->latest()->get();
        return view('penerima.orders', compact('orders'));
    }

    public function markTaken($id)
    {
        $order = Order::findOrFail($id);

        // Cek apakah user yang membuat order ini
        if ($order->created_by !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $old = $order->pickup_status;

        $order->update([
            'pickup_status' => 'taken'
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status_type' => 'pickup',
            'old_status' => $old,
            'new_status' => 'taken',
            'changed_by' => auth()->id()
        ]);

        return redirect('/penerima/orders')->with('success', 'Barang berhasil ditandai sebagai sudah diambil');
    }

    public function create()
    {
        $products = Product::all();
        return view('penerima.create', compact('products'));
    }

    public function unpaid(Request $request)
    {
        // Handle period parameter
        $period = $request->input('period', '7'); // Default: 7 hari
        
        if ($period === 'all') {
            $startDate = \Carbon\Carbon::createFromDate(2000, 1, 1)->startOfDay();
            $endDate = \Carbon\Carbon::now()->endOfDay();
        } else {
            $days = intval($period);
            $startDate = \Carbon\Carbon::now()->subDays($days)->startOfDay();
            $endDate = \Carbon\Carbon::now()->endOfDay();
        }

        $orders = Order::with(['items', 'items.product'])
            ->where('payment_status', '!=', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('kasir.orders', compact('orders', 'startDate', 'endDate'));
    }

    public function readyToPrint(Request $request)
    {
        // Handle period parameter
        $period = $request->input('period', '7');
        
        if ($period === 'all') {
            $startDate = \Carbon\Carbon::createFromDate(2000, 1, 1)->startOfDay();
            $endDate = \Carbon\Carbon::now()->endOfDay();
        } else {
            $days = intval($period);
            $startDate = \Carbon\Carbon::now()->subDays($days)->startOfDay();
            $endDate = \Carbon\Carbon::now()->endOfDay();
        }

        // Filter orders yang memiliki items dengan kategori cetak atau studio
        $orders = Order::with(['items', 'items.product'])
            ->where('print_status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('items.product', function($query) {
                $query->whereIn('category', ['cetak', 'print', 'studio']);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('operator.orders', compact('orders', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Parse JSON items jika dikirim sebagai string
            $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

            // Validate parsed items
            if (empty($items) || ! is_array($items)) {
                return back()->withErrors(['items' => 'Items must not be empty and must be an array']);
            }

            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'discount_amount' => 'nullable|numeric|min:0'
            ]);

            $createdBy = auth()->id();

            if (! $createdBy) {
                $createdBy = User::firstOrCreate(
                    ['email' => 'test@example.com'],
                    ['name' => 'Test User', 'password' => Hash::make('password')]
                )->id;
            }

            // Check if order contains print items (cetak/studio) or only barang
            $hasPrintItems = false;
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if (in_array(strtolower($product->category), ['cetak', 'print', 'studio'])) {
                    $hasPrintItems = true;
                    break;
                }
            }

            $order = Order::create([
                'order_code' => 'ORD-' . now()->timestamp,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'created_by' => $createdBy,
                'total_price' => 0,
                'discount_amount' => floatval($request->discount_amount ?? 0),
                'discount_percentage' => 0, // Persentase diskon akan di-set ke 0 karena diskon dalam nominal
                'payment_status' => 'unpaid',
                'print_status' => $hasPrintItems ? 'pending' : 'not_needed',
                'pickup_status' => 'waiting',
                'notes' => $request->notes ?? null
            ]);

            $total = 0;
            foreach ($items as $item) {

                $product = Product::findOrFail($item['product_id']);

                $price = isset($item['price']) ? floatval($item['price']) : $product->price;
                $qty = $item['quantity'];
                $discount = isset($item['discount']) ? floatval($item['discount']) : 0;
                $finalPrice = $price - $discount;

                $subtotal = $finalPrice * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;
            }

            // Note: Per-item discounts are already applied in the subtotal calculation above.
            // The discount_amount field here is stored for reference but should not be subtracted again.
            $discountAmount = floatval($request->discount_amount ?? 0);
            $order->update([
                'discount_amount' => $discountAmount,
                'total_price' => $total
            ]);

            DB::commit();

            return redirect()->route('orderstatus.index')->with('success', 'Order berhasil dibuat! Total: Rp ' . number_format($total, 0, ',', '.'));
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal membuat order: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            // Delete related records
            OrderItem::where('order_id', $order->id)->delete();
            OrderStatusLog::where('order_id', $order->id)->delete();

            // Delete the order
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus order: ' . $e->getMessage()
            ], 500);
        }
    }
}
