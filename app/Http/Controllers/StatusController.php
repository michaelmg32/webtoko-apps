<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderStatusLog;

class StatusController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Handle period parameter
        $period = request('period', '7'); // Default: 7 hari
        
        if ($period === 'all') {
            $startDate = \Carbon\Carbon::createFromDate(2000, 1, 1)->startOfDay();
            $endDate = \Carbon\Carbon::now()->endOfDay();
        } else {
            $days = intval($period);
            $startDate = \Carbon\Carbon::now()->subDays($days)->startOfDay();
            $endDate = \Carbon\Carbon::now()->endOfDay();
        }

        // Ambil orders berdasarkan role user
        $query = Order::with('items', 'items.product', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Semua role (penerima, kasir, operator, admin) bisa lihat semua orders
        // Tidak ada filter berdasarkan user

        // Get all orders untuk statistics
        $allOrders = $query->get();

        // Calculate statistics
        $unpaidOrders = $allOrders->where('payment_status', 'unpaid')->count();
        $notPrintedOrders = $allOrders->where('print_status', 'pending')->count();
        
        // Count orders not picked up (belum diambil) - count orders, not items
        $waitingItemsCount = $allOrders->where('pickup_status', 'waiting')->count();

        // Get paginated orders (recent first)
        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('orderstatus', [
            'orders' => $orders,
            'unpaidOrders' => $unpaidOrders,
            'notPrintedOrders' => $notPrintedOrders,
            'waitingItemsCount' => $waitingItemsCount,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }

    public function markPrinted($id)
    {
        $order = Order::findOrFail($id);

        $old = $order->print_status;

        $order->update([
            'print_status' => 'printed'
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status_type' => 'print',
            'old_status' => $old,
            'new_status' => 'printed',
            'changed_by' => Auth::id()
        ]);

        return redirect()->route('operator.orders.index')->with('success', 'Order ' . $order->order_code . ' berhasil ditandai sebagai sudah dicetak!');
    }

    public function receipt(Order $order)
    {
        if (Auth::user()->role === 'penerima' && $order->created_by !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product', 'payments');

        return view('receipt', compact('order'));
    }

    public function markTaken($id)
    {
        $order = Order::findOrFail($id);

        $old = $order->pickup_status;

        $order->update([
            'pickup_status' => 'taken'
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status_type' => 'pickup',
            'old_status' => $old,
            'new_status' => 'taken',
            'changed_by' => Auth::id()
        ]);

        return back();
    }

    public function updatePickupStatus($id, Request $request)
    {
        try {
            $order = Order::findOrFail($id);
            
            $validated = $request->validate([
                'pickup_status' => 'required|in:waiting,taken'
            ]);

            $old = $order->pickup_status;

            $order->update([
                'pickup_status' => $validated['pickup_status']
            ]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status_type' => 'pickup',
                'old_status' => $old,
                'new_status' => $validated['pickup_status'],
                'changed_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pickup status updated successfully',
                'new_status' => $validated['pickup_status']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            // Delete order items first
            $order->items()->delete();
            
            // Delete order payments
            $order->payments()->delete();
            
            // Delete order status logs
            OrderStatusLog::where('order_id', $order->id)->delete();
            
            // Delete the order
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function getOrder($id)
    {
        try {
            $order = Order::with('items.product', 'payments', 'customer')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function voidOrder($id, Request $request)
    {
        try {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only admins can void orders'
                ], 403);
            }

            // Validate password
            $request->validate([
                'password' => 'required|string'
            ]);

            // Verify password
            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 401);
            }

            $order = Order::findOrFail($id);
            
            // Delete order items first
            $order->items()->delete();
            
            // Delete order payments
            $order->payments()->delete();
            
            // Delete order status logs
            OrderStatusLog::where('order_id', $order->id)->delete();
            
            // Delete the order
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order voided successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
