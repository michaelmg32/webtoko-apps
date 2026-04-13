<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        // Get products with pagination
        $products = Product::latest()->paginate(5);

        // Calculate statistics
        // Low stock count for limited-stock products
        $lowStockCount = Product::where('unlimited_stock', false)
            ->where('stock', '<=', 5)
            ->count();

        // Most purchased category
        $mostPurchasedCategory = DB::table('order_items')
            ->select('products.category')
            ->selectRaw('COUNT(order_items.id) as total')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.category')
            ->orderByDesc('total')
            ->first();

        $mostPurchasedCategoryLabel = 'N/A';
        if ($mostPurchasedCategory) {
            $category = $mostPurchasedCategory->category;
            $mostPurchasedCategoryLabel = match(strtolower($category)) {
                'print', 'cetak' => 'Cetak',
                'studio' => 'Studio',
                'goods', 'barang' => 'Barang',
                default => ucfirst($category)
            };
        }

        // Total items count (total quantities across all products)
        $totalItems = DB::table('order_items')->sum('quantity') ?? 0;

        return view('admin.products', [
            'products' => $products,
            'lowStockCount' => $lowStockCount,
            'mostPurchasedCategory' => $mostPurchasedCategoryLabel,
            'totalItems' => $totalItems
        ]);
    }

    public function create()
    {
        return view('admin.create_product');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required|in:cetak,studio,barang,print,studio,goods',
            'price' => 'required|numeric|min:0',
            'amatir_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'unlimited_stock' => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['category'] = match(strtolower($data['category'])) {
            'print' => 'cetak',
            'studio session' => 'studio',
            'merchandise' => 'barang',
            default => $data['category']
        };
        $data['stock'] = $data['unlimited_stock'] ? 0 : ($data['stock'] ?? 0);
        $data['unlimited_stock'] = isset($data['unlimited_stock']) && $data['unlimited_stock'] ? true : false;

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.edit_product', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required|in:cetak,studio,barang,print,studio,goods',
            'price' => 'required|numeric|min:0',
            'amatir_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'unlimited_stock' => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['category'] = match(strtolower($data['category'])) {
            'print' => 'cetak',
            'studio session' => 'studio',
            'merchandise' => 'barang',
            default => $data['category']
        };
        $data['stock'] = $data['unlimited_stock'] ? 0 : ($data['stock'] ?? 0);
        $data['unlimited_stock'] = isset($data['unlimited_stock']) && $data['unlimited_stock'] ? true : false;

        $product = Product::findOrFail($id);
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus');
    }
}