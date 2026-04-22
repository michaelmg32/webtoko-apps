@extends('layouts.app')

@section('title', 'Print Queue - Operator')
@section('page-title', 'Antrian Print')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row md:items-end gap-6">
                <div class="flex-1 space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Cari Pesanan</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Masukkan kode order atau nama customer..."
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none">
                    </div>
                </div>

                <div class="w-full md:w-64 space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Periode Waktu</label>
                    <div class="relative">
                        <select id="timePeriod" onchange="applyTimePeriodFilter()"
                            class="w-full appearance-none px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                            <option value="1">1 Hari Terakhir</option>
                            <option value="7">7 Hari Terakhir</option>
                            <option value="30">1 Bulan Terakhir</option>
                            <option value="365">1 Tahun Terakhir</option>
                            <option value="all">Semua Waktu</option>
                        </select>
                        <i
                            class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button onclick="clearSearch()"
                        class="px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fas fa-redo-alt"></i> Reset
                    </button>
                </div>
            </div>
        </div>


        <div class="grid gap-5">
            @forelse($orders as $order)
                @php
                    $statusBadge = 'STANDARD';
                    $accentColor = 'blue';
                    $badgeClasses = 'bg-blue-50 text-blue-700 border-blue-100';

                    if ($order->items->where('product.category', 'like', '%special%')->count() > 0) {
                        $statusBadge = 'SPECIAL';
                        $accentColor = 'purple';
                        $badgeClasses = 'bg-purple-50 text-purple-700 border-purple-100';
                    } else if ($order->items->where('product.category', 'like', '%premium%')->count() > 0) {
                        $statusBadge = 'PREMIUM';
                        $accentColor = 'amber';
                        $badgeClasses = 'bg-amber-50 text-amber-700 border-amber-100';
                    }
                @endphp

                <div class="order-item bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group"
                    data-order-code="{{ $order->order_code }}" data-customer-name="{{ $order->customer_name }}"
                    data-created-date="{{ $order->created_at->timestamp }}">

                    <div class="flex flex-col md:flex-row">
                        <div class="w-2 bg-{{ $accentColor }}-500"></div>

                        <div class="flex-1 p-6">
                            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest border {{ $badgeClasses }}">
                                        {{ $statusBadge }}
                                    </span>
                                    <span
                                        class="text-sm font-mono font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded">#{{ $order->order_code }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-400 bg-gray-50 px-3 py-1.5 rounded-lg">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ $order->created_at->format('d M Y') }}</span>
                                    <span class="mx-1">•</span>
                                    <i class="far fa-clock"></i>
                                    <span>{{ $order->created_at->format('H:i') }}</span>
                                </div>
                            </div>

                            <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                                <div class="min-w-[200px]">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Customer</p>
                                    <h3 class="text-xl font-black text-gray-900 leading-tight truncate">
                                        {{ $order->customer_name }}</h3>
                                </div>

                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter mb-2">Order Items</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">
                                        @foreach($order->items as $item)
                                            <div
                                                class="flex items-center justify-between bg-gray-50/50 p-2 rounded-lg border border-transparent hover:border-gray-200 transition">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center font-bold text-blue-600 text-xs">
                                                        {{ $item->quantity }}x
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800">{{ $item->product_name ?? $item->product->name }}</p>
                                                        <p class="text-[10px] text-gray-500 uppercase">
                                                            {{ $item->product->category }}</p>
                                                    </div>
                                                </div>
                                                <span
                                                    class="text-sm font-semibold text-gray-700">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50/50 border-l border-gray-100 p-6 flex flex-col items-center justify-center min-w-[200px] gap-4">
                            <div class="text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Waktu Tunggu</p>
                                <p class="text-sm font-bold text-{{ $accentColor }}-600">
                                    {{ $order->created_at->diffForHumans() }}</p>
                            </div>

                            <form action="{{ route('operator.orders.print', $order->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-xl font-bold shadow-lg shadow-emerald-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-print"></i>
                                    <span>MARK AS PRINTED</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl shadow-sm border-2 border-dashed border-gray-200 p-20 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-50 rounded-full mb-6">
                        <i class="fas fa-check-double text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-800">Semua Beres!</h3>
                    <p class="text-gray-500 mt-2">Tidak ada pesanan di antrean cetak saat ini.</p>
                </div>
            @endforelse
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 px-4">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest transition-all">Sistem
                        Online</span>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><i
                        class="fas fa-terminal mr-1"></i> OPERATOR-01</span>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">© {{ now()->year }} Bukit Foto Studio</p>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', performSearch);

        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            const orderItems = document.querySelectorAll('.order-item');

            orderItems.forEach(item => {
                const orderCode = item.dataset.orderCode.toLowerCase();
                const customerName = item.dataset.customerName.toLowerCase();

                if (orderCode.includes(searchTerm) || customerName.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.classList.add('animate-fadeIn'); // Tambahkan animasi jika perlu
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function clearSearch() {
            searchInput.value = '';
            performSearch();
        }

        // Time period filter functionality
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const period = urlParams.get('period') || '7';
            document.getElementById('timePeriod').value = period;
        });

        function applyTimePeriodFilter() {
            const timePeriod = document.getElementById('timePeriod').value;
            const url = new URL(window.location);
            url.searchParams.set('period', timePeriod);
            window.location = url.toString();
        }

        // Auto refresh setiap 30 detik
        setInterval(() => {
            // Hanya refresh jika input pencarian kosong agar tidak mengganggu operator
            if (searchInput.value === "") {
                location.reload();
            }
        }, 30000);
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
@endsection