@extends('layouts.app')

@section('title', 'Order Status - Studio Presisi')
@section('page-title', 'Status Pesanan')

@section('content')
<div class="space-y-6 animate-in fade-in duration-500">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500 flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Belum Dibayar</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $unpaidOrders ?? 0 }}</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-red-600">
                <i class="fas fa-credit-card text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Belum Dicetak</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $notPrintedOrders ?? 0 }}</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-4 text-purple-600">
                <i class="fas fa-print text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500 flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Belum Diambil</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $waitingItemsCount ?? 0 }}</p>
            </div>
            <div class="bg-orange-50 rounded-xl p-4 text-orange-600">
                <i class="fas fa-box text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-slate-800">Pesanan Studio Terbaru</h2>
            @if(auth()->user()->role === 'penerima' || auth()->user()->role === 'admin')
                <a href="{{ route('penerima.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 transition font-bold shadow-lg shadow-green-100">
                    <i class="fas fa-plus"></i> Buat Pesanan Baru
                </a>
            @endif
        </div>

        <div class="p-6 bg-slate-50/50 border-b border-slate-100">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan kode order, nama pelanggan..." 
                            class="w-full pl-11 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm font-medium">
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select id="timePeriod" onchange="applyTimePeriodFilter()" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 bg-white text-sm font-bold text-slate-700">
                        <option value="1">1 Hari Terakhir</option>
                        <option value="7">7 Hari Terakhir</option>
                        <option value="30">1 Bulan Terakhir</option>
                        <option value="365">1 Tahun Terakhir</option>
                        <option value="all">Semua Waktu</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex items-center">
                <input type="checkbox" id="hideCompletedCheckbox" checked class="w-5 h-5 text-green-600 rounded border-slate-300 focus:ring-green-500 cursor-pointer">
                <label for="hideCompletedCheckbox" class="ml-3 text-sm font-bold text-slate-600 cursor-pointer">
                    Sembunyikan Pesanan Selesai (Diambil)
                </label>
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($orders->count() > 0)
                <table class="w-full" id="ordersTable">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-left">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Pesanan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pembayaran</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Cetak</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pengambilan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100">
                        @foreach($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition order-row" 
                            data-order-id="{{ $order->id }}"
                            data-customer-name="{{ $order->customer_name ?? $order->customer->name ?? 'N/A' }}"
                            data-order-code="{{ $order->order_code ?? $order->code ?? 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) }}"
                            data-date="{{ $order->created_at?->getTimestamp() ?? 0 }}"
                            data-amount="{{ $order->total_price ?? 0 }}"
                            data-status="{{ $order->pickup_status ?? 'waiting' }}"
                            data-pickup-status="{{ $order->pickup_status ?? 'waiting' }}">
                            
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-bold text-slate-800 text-base customer-name">{{ $order->customer_name ?? $order->customer->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500 order-date">{{ $order->created_at?->format('d M Y H:i') ?? 'N/A' }}</p>
                                    <span class="inline-block mt-1 text-[11px] font-bold px-2 py-0.5 bg-slate-100 text-slate-600 rounded order-code">
                                        {{ $order->order_code ?? $order->code ?? 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $paymentStatus = $order->payment_status ?? 'unpaid';
                                    $paymentBadge = match($paymentStatus) {
                                        'paid' => 'bg-green-100 text-green-700',
                                        'partial' => 'bg-amber-100 text-amber-700',
                                        default => 'bg-red-100 text-red-700'
                                    };
                                    $paymentLabel = match($paymentStatus) {
                                        'paid' => 'Sudah Dibayar',
                                        'partial' => 'Bayar Setengah (DP)',
                                        default => 'Belum Dibayar'
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-xs font-black uppercase {{ $paymentBadge }}">
                                    {{ $paymentLabel }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $printStatus = $order->print_status ?? 'not_needed';
                                    $printBadge = match($printStatus) {
                                        'printed' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        default => 'bg-slate-100 text-slate-500'
                                    };
                                    $printLabel = match($printStatus) {
                                        'printed' => 'Sudah Dicetak',
                                        'pending' => 'Belum Cetak',
                                        default => 'Tidak Ada Cetak'
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-xs font-black uppercase {{ $printBadge }}">
                                    {{ $printLabel }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $pickupStatus = $order->pickup_status ?? 'waiting';
                                    $statusBadge = $pickupStatus === 'taken' ? 'bg-slate-200 text-slate-600' : 'bg-blue-600 text-white shadow-md shadow-blue-100';
                                    $statusLabel = $pickupStatus === 'taken' ? 'Sudah Diambil' : 'Belum Diambil';
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg text-xs font-black uppercase {{ $statusBadge }} status-badge">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-green-600 hover:text-white transition change-status-btn" 
                                        title="Change Status" data-status="{{ $pickupStatus }}">
                                        <i class="fas fa-check-square"></i>
                                    </button>
                                    <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white transition view-detail-btn" 
                                        title="View Details" data-order-id="{{ $order->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-20 text-center">
                    <i class="fas fa-inbox text-6xl text-slate-200 mb-4"></i>
                    <p class="text-slate-500 font-bold">Tidak ada pesanan</p>
                </div>
            @endif
        </div>

        <!-- Expand Button -->
        <div id="expandContainer" class="mt-8 flex justify-center pb-6">
            <button id="expandBtn" type="button" 
                class="inline-flex items-center gap-2 px-8 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-all shadow-lg active:scale-95">
                <i class="fas fa-chevron-down"></i>
                <span>Muat Lebih Banyak</span>
            </button>
        </div>
    </div>
</div>

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
                <div class="flex justify-between items-center py-2">
                    <span class="text-slate-500 font-bold text-sm">Total Jumlah</span>
                    <span id="detailAmount" class="text-2xl font-black text-green-600">-</span>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Item Pesanan</p>
                <div id="detailItems" class="space-y-3">
                    </div>
            </div>

            <div id="discountSection" class="hidden p-4 bg-red-50 rounded-xl border border-red-100">
                <div class="flex justify-between items-center">
                    <span class="text-red-700 font-bold text-sm">Total Diskon yang Diterapkan</span>
                    <span id="detailDiscount" class="text-red-700 font-black text-lg">-</span>
                </div>
            </div>

            <div id="notesSection" class="hidden p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-[10px] font-black text-blue-600 uppercase mb-2 tracking-widest">📝 Catatan Pesanan</p>
                <p id="detailNotes" class="text-sm text-blue-800 italic whitespace-pre-wrap break-words">-</p>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">
            <button onclick="voidOrder()" class="flex-1 py-3.5 bg-red-50 text-red-600 rounded-2xl font-black text-sm hover:bg-red-100 transition-all border border-red-200">
                BATALKAN PESANAN
            </button>
            <button onclick="closeDetailModal()" class="flex-1 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-slate-800 transition-all">
                TUTUP
            </button>
        </div>
    </div>
</div>

<div id="statusModal" class="hidden fixed inset-0 bg-slate-900/60 flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8">
        <h3 class="text-lg font-black text-slate-900 mb-2">Ubah Status Pengambilan</h3>
        <p class="text-xs text-slate-500 mb-6 uppercase tracking-wider">Pesanan: <span id="modalOrderCode" class="font-bold text-slate-800">-</span></p>
        
        <div class="space-y-3 mb-8">
            <label class="flex items-center p-4 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all group">
                <input type="radio" name="pickupStatus" value="waiting" class="w-5 h-5 text-green-600 focus:ring-green-500">
                <span class="ml-4 font-bold text-slate-700 group-hover:text-slate-900">Belum Diambil</span>
            </label>
            <label class="flex items-center p-4 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all group">
                <input type="radio" name="pickupStatus" value="taken" class="w-5 h-5 text-green-600 focus:ring-green-500">
                <span class="ml-4 font-bold text-slate-700 group-hover:text-slate-900">Sudah Diambil</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button onclick="closeModal()" class="flex-1 py-3 text-slate-500 font-bold hover:text-slate-700">Batal</button>
            <button onclick="updateStatus()" class="flex-1 py-3 bg-green-600 text-white rounded-xl font-black shadow-lg shadow-green-100 hover:bg-green-700 transition-all">PERBARUI</button>
        </div>
    </div>
</div>

<div id="receiptModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <p class="text-lg font-black text-slate-800">Struk Pesanan</p>
                <p class="text-xs text-slate-500 font-medium tracking-tight">Pratinjau struk sebelum cetak</p>
            </div>
            <button type="button" onclick="closeReceiptModal()" class="text-slate-400 hover:text-slate-800 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 bg-slate-50/50 overflow-y-auto max-h-[70vh]" id="receiptContent"></div>
    </div>
</div>

<div id="voidOrderModal" class="hidden fixed inset-0 bg-slate-900/70 flex items-center justify-center z-[80] p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 border border-red-100">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-2xl mb-6 mx-auto">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="text-xl font-black text-slate-900 text-center mb-2 uppercase">Batalkan Pesanan</h3>
        <p class="text-sm text-slate-500 text-center mb-6">Masukkan password untuk mengkonfirmasi pembatalan pesanan.</p>
        
        <div class="mb-6">
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Kata Sandi Admin</label>
            <input type="password" id="voidPasswordInput" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none font-bold" placeholder="••••••••">
            <p id="voidErrorMsg" class="text-xs text-red-600 mt-2 font-bold hidden"></p>
        </div>

        <div class="flex gap-3">
            <button onclick="closeVoidModal()" class="flex-1 py-3 text-slate-500 font-bold">Batal</button>
            <button onclick="confirmVoidOrder()" class="flex-1 py-3 bg-red-600 text-white rounded-xl font-black shadow-lg shadow-red-100">BATALKAN</button>
        </div>
    </div>
</div>

<script>
    // --- EXPAND BUTTON + FILTER LOGIC ---
    let itemsShown = 15; // Start with 15 items
    const itemsPerLoad = 15; // Load 15 more on each click
    let currentOrderId = null;
    let allOrders = @json($orders);
    let isLoading = false;

    // Expand Button Setup - HARUS SEBELUM filterTable()
    const expandBtn = document.getElementById('expandBtn');
    const expandContainer = document.getElementById('expandContainer');

    console.log('Button elements:', { expandBtn, expandContainer });

    // Initialize dropdown value from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const currentPeriod = urlParams.get('period') || '7';
    document.getElementById('timePeriod').value = currentPeriod;

    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('hideCompletedCheckbox').addEventListener('change', filterTable);
    
    filterTable();

    if (expandBtn) {
        expandBtn.addEventListener('click', function(e) {
            console.log('Button clicked!', { isLoading, itemsShown, itemsPerLoad });
            loadMoreOrders();
        });
    } else {
        console.warn('expandBtn not found!');
    }

    function updateOrderVisibility() {
        const allRows = document.querySelectorAll('.order-row');
        
        // Count visible rows berdasarkan current filter
        let visibleIndex = 0;
        let totalVisible = 0;
        
        allRows.forEach((row) => {
            const isFiltered = row.getAttribute('data-filtered') !== 'false';
            
            // Skip jika di-filter out
            if (!isFiltered) {
                row.style.display = 'none';
                return;
            }
            
            totalVisible++;
            if (visibleIndex < itemsShown) {
                row.style.display = '';
                visibleIndex++;
            } else {
                row.style.display = 'none';
            }
        });

        console.log('updateOrderVisibility:', { totalVisible, itemsShown, shouldShowButton: totalVisible > itemsShown, totalRows: allRows.length });

        // Tampilkan button jika ada lebih banyak yang bisa di-load
        if (totalVisible > itemsShown) {
            expandContainer.style.display = 'flex';
        } else {
            expandContainer.style.display = 'none';
        }
    }

    function loadMoreOrders() {
        console.log('loadMoreOrders called - before:', { isLoading, itemsShown });
        if (isLoading) {
            console.warn('Already loading, skipping');
            return;
        }
        isLoading = true;
        expandBtn.disabled = true;
        expandBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div><span>Memuat...</span>';
        
        itemsShown += itemsPerLoad;
        console.log('loadMoreOrders - after increment:', { itemsShown });
        
        setTimeout(() => {
            console.log('Calling updateOrderVisibility from setTimeout');
            updateOrderVisibility();
            expandBtn.disabled = false;
            expandBtn.innerHTML = '<i class="fas fa-chevron-down"></i><span>Muat Lebih Banyak</span>';
            isLoading = false;
            console.log('loadMoreOrders completed');
        }, 300);
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-detail-btn')) {
            const btn = e.target.closest('.view-detail-btn');
            const orderId = btn.dataset.orderId;
            fetch(`/api/orders/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) openDetailModal(data.order);
                    else showNotification('Gagal load order details', 'error');
                })
                .catch(error => showNotification('Gagal memuat detail pesanan', 'error'));
        }
    });

    document.querySelectorAll('.change-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.order-row');
            currentOrderId = row.dataset.orderId;
            const currentStatus = row.dataset.status;
            const orderCode = row.querySelector('.order-code').textContent;
            document.getElementById('modalOrderCode').textContent = orderCode;
            document.querySelector(`input[name="pickupStatus"][value="${currentStatus}"]`).checked = true;
            document.getElementById('statusModal').classList.remove('hidden');
        });
    });

    function closeModal() { document.getElementById('statusModal').classList.add('hidden'); }
    function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }

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
                        <span class="font-black text-slate-800 text-sm">${item.product?.name || item.product_name}</span>
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

    function voidOrder() {
        const userRole = '{{ auth()->user()->role ?? null }}';
        if (userRole !== 'admin') { showNotification('Hanya admin yang bisa void order', 'warning'); return; }
        const orderId = document.getElementById('detailModal').dataset.orderId;
        document.getElementById('voidOrderModal').classList.remove('hidden');
        document.getElementById('voidOrderModal').dataset.orderId = orderId;
    }

    function closeVoidModal() { document.getElementById('voidOrderModal').classList.add('hidden'); }

    function confirmVoidOrder() {
        const orderId = document.getElementById('voidOrderModal').dataset.orderId;
        const password = document.getElementById('voidPasswordInput').value;
        const errorMsg = document.getElementById('voidErrorMsg');
        fetch(`/orderstatus/${orderId}/void`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type': 'application/json'},
            body: JSON.stringify({ password: password })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { errorMsg.textContent = data.message; errorMsg.classList.remove('hidden'); return; }
            location.reload();
        });
    }

    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const hideTaken = document.getElementById('hideCompletedCheckbox').checked;
        
        console.log('filterTable called:', { search, hideTaken });
        
        // Filter rows based on search & checkbox
        let filteredCount = 0;
        document.querySelectorAll('.order-row').forEach(row => {
            const text = (row.dataset.customerName || '').toLowerCase() + (row.dataset.orderCode || '').toLowerCase();
            const isTaken = row.dataset.status === 'taken';
            const matchesSearch = text.includes(search);
            const shouldShow = matchesSearch && (!hideTaken || !isTaken);
            
            row.setAttribute('data-filtered', shouldShow ? 'true' : 'false');
            if (shouldShow) filteredCount++;
        });
        
        console.log('filterTable - filtered rows found:', filteredCount);
        itemsShown = 15; // Reset to 15 when filtering
        updateOrderVisibility();
    }

    function updateStatus() {
        const selectedStatus = document.querySelector('input[name="pickupStatus"]:checked').value;
        
        if (!currentOrderId) {
            showNotification('Order ID tidak ditemukan', 'error');
            return;
        }

        fetch(`/orders/${currentOrderId}/update-pickup-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                pickup_status: selectedStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`[data-order-id="${currentOrderId}"]`);
                if (row) {
                    row.dataset.status = selectedStatus;
                    const statusBadge = row.querySelector('.status-badge');
                    const statusText = selectedStatus === 'taken' ? 'Sudah Diambil' : 'Belum Diambil';
                    const statusClass = selectedStatus === 'taken' 
                        ? 'bg-slate-200 text-slate-600' 
                        : 'bg-blue-600 text-white shadow-md shadow-blue-100';
                    
                    statusBadge.textContent = statusText;
                    statusBadge.className = `px-3 py-1.5 rounded-lg text-xs font-black uppercase ${statusClass} status-badge`;
                }
                
                closeModal();
                showNotification('Status berhasil diperbarui!', 'success');
                
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Gagal perbarui status: ' + (data.message || 'Error tidak diketahui'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error memperbarui status', 'error');
        });
    }

    function closeReceiptModal() { document.getElementById('receiptModal').classList.add('hidden'); }

    function applyTimePeriodFilter() {
        const timePeriod = document.getElementById('timePeriod').value;
        const url = new URL(window.location);
        url.searchParams.set('period', timePeriod);
        window.location = url.toString();
    }
</script>
@endsection