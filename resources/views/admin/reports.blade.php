@extends('layouts.app')

@section('title', 'Financial Reports - Admin')
@section('page-title', 'Financial Performance')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 pb-0">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Financial Performance</h2>
                    <p class="text-gray-500 text-sm mt-1 flex items-center gap-2">
                        <i class="far fa-calendar-alt"></i>
                        @if($period === 'daily')
                            {{ now()->format('d F Y') }}
                        @elseif($period === 'monthly')
                            {{ now()->format('F Y') }}
                        @else
                            {{ now()->format('Y') }}
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="flex gap-2 overflow-x-auto border-b border-gray-100 no-scrollbar">
                <a href="{{ route('admin.reports.index', ['period' => 'daily']) }}" 
                   class="px-6 py-3.5 text-sm font-medium transition-all border-b-2 whitespace-nowrap {{ $period === 'daily' ? 'text-blue-600 border-blue-600 bg-blue-50/50' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-200' }}">
                    Daily Report
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'monthly']) }}" 
                   class="px-6 py-3.5 text-sm font-medium transition-all border-b-2 whitespace-nowrap {{ $period === 'monthly' ? 'text-blue-600 border-blue-600 bg-blue-50/50' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-200' }}">
                    Monthly Report
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'yearly']) }}" 
                   class="px-6 py-3.5 text-sm font-medium transition-all border-b-2 whitespace-nowrap {{ $period === 'yearly' ? 'text-blue-600 border-blue-600 bg-blue-50/50' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-200' }}">
                    Yearly Report
                </a>
            </div>
        </div>
    </div>

    @if($period !== 'cashflow')
    
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl shadow-md p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-40 h-40 rounded-full bg-white opacity-10 blur-2xl"></div>
        <div class="absolute bottom-0 right-10 w-20 h-20 rounded-full bg-white opacity-10 blur-xl"></div>
        
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium uppercase tracking-wider mb-2">Total Revenue Today</p>
                <p class="text-4xl md:text-5xl font-extrabold text-white tracking-tight">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                <div class="flex items-center gap-2 mt-4 text-blue-100/80 text-sm">
                    <i class="fas fa-info-circle"></i> 
                    <span>Based on actual payment date</span>
                </div>
            </div>
            <div class="hidden md:flex bg-white/20 backdrop-blur-sm rounded-2xl p-5 shadow-inner">
                <i class="fas fa-wallet text-white text-4xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Revenue by Category</h3>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600"><i class="fas fa-layer-group"></i></div>
            </div>
            
            @if($revenueByCategory->count() > 0)
                <div class="space-y-5 flex-1">
                    @foreach($revenueByCategory as $category)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-600 text-sm">{{ ucfirst($category->category) }}</span>
                                <span class="text-gray-900 font-bold text-sm">Rp {{ number_format($category->total_revenue ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ ($category->total_revenue / ($revenueByCategory->sum('total_revenue') ?: 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 flex-1 opacity-60">
                    <i class="fas fa-chart-pie text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm">No revenue data available</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Revenue by Payment Method</h3>
                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600"><i class="fas fa-credit-card"></i></div>
            </div>
            
            @if($revenueByPaymentMethod->count() > 0)
                <div class="space-y-5 flex-1">
                    @foreach($revenueByPaymentMethod as $method)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-600 text-sm flex items-center gap-2">
                                    @switch($method->payment_method)
                                        @case('cash')
                                            <i class="fas fa-money-bill-wave text-emerald-500"></i> Cash
                                            @break
                                        @case('transfer')
                                            <i class="fas fa-university text-blue-500"></i> Transfer
                                            @break
                                        @default
                                            {{ ucfirst($method->payment_method) }}
                                    @endswitch
                                </span>
                                <span class="text-gray-900 font-bold text-sm">Rp {{ number_format($method->total_revenue ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ ($method->total_revenue / ($revenueByPaymentMethod->sum('total_revenue') ?: 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 flex-1 opacity-60">
                    <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm">No payment method data</p>
                </div>
            @endif
        </div>

    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Laporan Order Dibayar</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar transaksi yang telah diselesaikan</p>
            </div>
            
            <a href="{{ route('admin.reports.export', array_merge(['period' => $period], request()->query())) }}" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 rounded-lg font-semibold text-sm transition-colors border border-emerald-200">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>

        <div class="p-6 bg-gray-50/50 border-b border-gray-100">
            <form id="autoFilterForm" method="GET" action="{{ route('admin.reports.index') }}">
                <input type="hidden" name="period" value="{{ $period }}">
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4 items-end">
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="search" name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari order code atau nama..."
                                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <label for="filter_start_date" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Start Date</label>
                        <input type="date" id="filter_start_date" name="filter_start_date" 
                               value="{{ request('filter_start_date', $startDate->toDateString()) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                    </div>

                    <div class="lg:col-span-1">
                        <label for="filter_end_date" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">End Date</label>
                        <input type="date" id="filter_end_date" name="filter_end_date" 
                               value="{{ request('filter_end_date', $endDate->toDateString()) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                    </div>

                    <div class="lg:col-span-1">
                        <label for="filter_category" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Category</label>
                        <select id="filter_category" name="filter_category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                            <option value="">All</option>
                            <option value="cetak" {{ request('filter_category') === 'cetak' ? 'selected' : '' }}>Cetak</option>
                            <option value="studio" {{ request('filter_category') === 'studio' ? 'selected' : '' }}>Studio</option>
                            <option value="barang" {{ request('filter_category') === 'barang' ? 'selected' : '' }}>Barang</option>
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <label for="filter_payment_method" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Method</label>
                        <select id="filter_payment_method" name="filter_payment_method" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                            <option value="">All</option>
                            <option value="cash" {{ request('filter_payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ request('filter_payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <a href="{{ route('admin.reports.index', ['period' => $period]) }}" class="w-full px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center" title="Reset Filters">
                            <i class="fas fa-redo text-xs"></i>
                        </a>
                    </div>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const filterForm = document.getElementById('autoFilterForm');
                    const filterInputs = filterForm.querySelectorAll('input[type="date"], select');
                    const searchInput = document.getElementById('search');
                    let timeout = null;
                    
                    filterInputs.forEach(input => {
                        input.addEventListener('change', function() {
                            filterForm.submit();
                        });
                    });

                    if (searchInput) {
                        searchInput.addEventListener('keyup', function(e) {
                            clearTimeout(timeout);
                            timeout = setTimeout(function() {
                                filterForm.submit();
                            }, 500);
                        });
                    }
                });
            </script>
        </div>

        @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Order Info</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe Pembayaran</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Jumlah Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentOrders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors group cursor-pointer" onclick="openOrderDetail({{ $order->order_id }})">
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($order->payment_date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->payment_date)->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-blue-600">{{ $order->categories }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $order->order_code }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                {{ $order->customer_name }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeClass = 'bg-gray-50 text-gray-700 ring-gray-600/20';
                                    if (strpos($order->payment_type_label, 'DP') !== false) {
                                        $typeClass = 'bg-blue-50 text-blue-700 ring-blue-600/20';
                                    } elseif (strpos($order->payment_type_label, 'Bayar Penuh') !== false) {
                                        $typeClass = 'bg-purple-50 text-purple-700 ring-purple-600/20';
                                    } elseif (strpos($order->payment_type_label, 'Pelunasan') !== false) {
                                        $typeClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
                                    }
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $typeClass }}">
                                    {{ $order->payment_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $methodClass = 'bg-gray-50 text-gray-700 ring-gray-600/20';
                                    $methodIcon = 'fa-wallet';
                                    
                                    if (strpos($order->payment_method_label, '+') !== false) {
                                        $methodClass = 'bg-purple-50 text-purple-700 ring-purple-600/20';
                                        $methodIcon = 'fa-layer-group';
                                    } elseif (strpos(strtolower($order->payment_method_label), 'cash') !== false) {
                                        $methodClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
                                        $methodIcon = 'fa-money-bill-wave';
                                    } elseif (strpos(strtolower($order->payment_method_label), 'transfer') !== false) {
                                        $methodClass = 'bg-blue-50 text-blue-700 ring-blue-600/20';
                                        $methodIcon = 'fa-university';
                                    } 
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $methodClass }}">
                                    <i class="fas {{ $methodIcon }} text-[10px]"></i> {{ $order->payment_method_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    Rp {{ number_format($order->total_paid, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    Menampilkan total <span class="font-bold text-gray-900">{{ $recentOrders->count() }}</span> pesanan
                </p>
            </div>
        @else
            <div class="text-center py-16 px-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data transaksi</h3>
                <p class="text-gray-500 text-sm max-w-sm mx-auto">Belum ada order yang dibayar pada periode dan filter yang Anda pilih saat ini.</p>
            </div>
        @endif
    </div>
</div>

<!-- Detail Modal (dari orderstatus.blade.php) -->
<div id="detailModal" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Ringkasan Pesanan</h3>
            <button onclick="closeDetailModal()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="detailContent" class="p-8 space-y-6 overflow-y-auto">
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Nama Pelanggan</p>
                        <span class="text-xl font-black text-slate-900" id="detailCustomer">-</span>
                    </div>
                    <span id="detailPaymentMethod" class="px-4 py-1.5 rounded-xl text-xs font-black border">-</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Kode Pesanan</p>
                        <p id="detailOrderCode" class="text-sm font-bold text-slate-700">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">WhatsApp/Telepon</p>
                        <p id="detailPhone" class="text-sm font-bold text-slate-700">-</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold text-sm">Tanggal Pesanan</span>
                    <span id="detailDate" class="text-slate-800 font-bold text-sm">-</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold text-sm">Waktu Pengambilan</span>
                    <span id="detailPickupTime" class="text-slate-800 font-bold text-sm">-</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold text-sm">Total Harga</span>
                    <span id="detailAmount" class="text-2xl font-black text-green-600">-</span>
                </div>
            </div>

            <div id="paymentSection" class="hidden p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-[10px] font-black text-blue-600 uppercase mb-3 tracking-widest">💳 Informasi Pembayaran</p>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-blue-700 font-bold">Status Pembayaran</span>
                        <span id="detailPaymentStatus" class="text-sm font-black text-blue-900">-</span>
                    </div>
                    <div id="dpInfoSection" class="hidden border-t border-blue-200 pt-2">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-blue-700 font-bold">Jumlah DP</span>
                            <span id="detailDPAmount" class="text-sm font-black text-green-600">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-blue-700 font-bold">Sisa Pembayaran</span>
                            <span id="detailRemainingAmount" class="text-sm font-black text-red-600">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Item Pesanan</p>
                <div id="detailItems" class="space-y-3"></div>
            </div>

            <div id="notesSection" class="hidden p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-[10px] font-black text-blue-600 uppercase mb-2 tracking-widest">📝 Catatan Pesanan</p>
                <p id="detailNotes" class="text-sm text-blue-800 italic whitespace-pre-wrap break-words">-</p>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">
            <button onclick="closeDetailModal()" class="flex-1 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-slate-800 transition-all">
                TUTUP
            </button>
        </div>
    </div>
</div>

<script>
    function openOrderDetail(orderId) {
        fetch(`/api/orders/${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) openDetailModal(data.order);
                else showNotification('Gagal load order details', 'error');
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Gagal memuat detail pesanan', 'error');
            });
    }

    function closeDetailModal() { 
        document.getElementById('detailModal').classList.add('hidden'); 
    }

    function openDetailModal(order) {
        document.getElementById('detailModal').dataset.orderId = order.id;
        document.getElementById('detailCustomer').textContent = order.customer_name || (order.customer ? order.customer.name : '-');
        document.getElementById('detailOrderCode').textContent = order.order_code || order.code;
        document.getElementById('detailPhone').textContent = order.customer_phone || (order.customer ? order.customer.phone : '-');
        document.getElementById('detailAmount').textContent = 'Rp ' + Number(order.total_price).toLocaleString('id-ID');
        document.getElementById('detailDate').textContent = order.created_at ? new Date(order.created_at).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'}) : '-';

        // Payment Badge Logic
        let methods = [];
        if (order.payments && order.payments.length > 0) {
            order.payments.forEach(p => methods.push(p.payment_method === 'transfer' ? 'Transfer' : 'Cash'));
        }
        const finalMethod = methods.length > 0 ? [...new Set(methods)].join('+') : 'Belum Dibayar';
        const badge = document.getElementById('detailPaymentMethod');
        badge.textContent = finalMethod;
        badge.className = finalMethod === 'Belum Dibayar' ? 'px-3 py-1 rounded-lg text-xs font-black border border-red-200 bg-red-50 text-red-700' : 'px-3 py-1 rounded-lg text-xs font-black border border-green-200 bg-green-50 text-green-700';

        // Payment Status Display - Get DP and Pelunasan breakdown
        const paymentStatus = order.payment_status || 'unpaid';
        const paymentSection = document.getElementById('paymentSection');
        const dpInfoSection = document.getElementById('dpInfoSection');
        const totalPrice = Number(order.total_price || 0);
        
        // Calculate DP and Pelunasan separately
        const dpAmount = order.payments 
            ? order.payments.filter(p => p.payment_type === 'dp' || !p.payment_type).reduce((sum, p) => sum + Number(p.amount || 0), 0)
            : 0;
        const pelunasanAmount = order.payments
            ? order.payments.filter(p => p.payment_type === 'pelunasan').reduce((sum, p) => sum + Number(p.amount || 0), 0)
            : 0;
        const fullPaymentAmount = order.payments
            ? order.payments.filter(p => p.payment_type === 'full_payment').reduce((sum, p) => sum + Number(p.amount || 0), 0)
            : 0;
        
        const totalPaid = dpAmount + pelunasanAmount + fullPaymentAmount;
        const remainingAmount = totalPrice - totalPaid;
        
        // Show payment info ONLY if there are DP or multiple payments (not full payment in single transaction)
        const hasDP = dpAmount > 0;
        const hasMultiplePayments = (order.payments && order.payments.length > 1);

        if ((hasDP || (hasMultiplePayments && paymentStatus !== 'unpaid')) && order.payments && order.payments.length > 0) {
            paymentSection.classList.remove('hidden');
            
            // Set payment status text
            let statusText = '';
            if (paymentStatus === 'paid') {
                statusText = '✓ Sudah Dibayar Penuh';
            } else if (paymentStatus === 'partial') {
                statusText = '⏳ Belum Lunas (DP)';
            }
            document.getElementById('detailPaymentStatus').textContent = statusText;

            // Show DP breakdown
            dpInfoSection.classList.remove('hidden');
            document.getElementById('detailDPAmount').textContent = 'Rp ' + dpAmount.toLocaleString('id-ID');
            
            // If paid (lunas) - show what was paid after DP
            // If partial (belum lunas) - show remaining amount to pay
            if (paymentStatus === 'paid') {
                const paymentAfterDP = pelunasanAmount + fullPaymentAmount;
                document.getElementById('detailRemainingAmount').textContent = 'Rp ' + paymentAfterDP.toLocaleString('id-ID');
            } else {
                document.getElementById('detailRemainingAmount').textContent = 'Rp ' + remainingAmount.toLocaleString('id-ID');
            }
        } else {
            paymentSection.classList.add('hidden');
        }

        // Render Items with Discount
        const container = document.getElementById('detailItems');
        container.innerHTML = (order.items || []).map(item => {
            const price = Number(item.price || 0);
            const discount = Number(item.discount || 0);
            const qty = Number(item.quantity || item.qty || 1);
            const finalPrice = price - discount;
            return `
                <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-black text-slate-800 text-sm">${item.product_name || item.product?.name}</span>
                        <span class="text-xs font-black text-slate-400">×${qty}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-black text-slate-700">Rp ${finalPrice.toLocaleString('id-ID')}</span>
                        ${discount > 0 ? `<span class="text-xs text-slate-400 line-through">Rp ${price.toLocaleString('id-ID')}</span>` : ''}
                    </div>
                </div>
            `;
        }).join('');

        // Handle Notes Display
        const notesSection = document.getElementById('notesSection');
        const detailNotes = document.getElementById('detailNotes');
        if (order.notes && order.notes.trim() !== '') {
            detailNotes.textContent = order.notes;
            notesSection.classList.remove('hidden');
        } else {
            notesSection.classList.add('hidden');
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }
</script>

@endsection