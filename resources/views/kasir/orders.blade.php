@extends('layouts.app')

@section('title', 'Unpaid Orders - Kasir')
@section('page-title', 'Halaman Pembayaran')

@section('content')
<div class="grid grid-cols-3 gap-6">
    <!-- LEFT SIDE: UNPAID ORDERS LIST -->
    <div class="col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Pembayaran Pesanan</h2>
                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-semibold text-sm">
                        {{ count($orders) }} PENDING
                    </span>
                </div>
                
                <!-- Filter Buttons -->
                <div class="flex gap-2 mt-4 mb-4">
                    <button onclick="filterOrders('unpaid')" class="filter-btn active px-4 py-2 rounded-xl text-sm font-bold border border-red-500 bg-red-50 text-red-700 transition-all" data-filter="unpaid">
                        <i class="fas fa-times-circle"></i> Belum Bayar
                    </button>
                    <button onclick="filterOrders('partial')" class="filter-btn px-4 py-2 rounded-xl text-sm font-bold border border-gray-200 text-gray-700 hover:border-yellow-500 hover:bg-yellow-50 transition-all" data-filter="partial"
                        <i class="fas fa-check"></i> Sudah DP
                    </button>
                </div>

                <!-- Time Period Filter -->
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-2">PERIODE</label>
                    <select id="timePeriod" onchange="applyTimePeriodFilter()" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="1">1 Hari Terakhir</option>
                        <option value="7">7 Hari Terakhir</option>
                        <option value="30">1 Bulan Terakhir</option>
                        <option value="365">1 Tahun Terakhir</option>
                        <option value="all">Semua Waktu</option>
                    </select>
                </div>
            </div>

            <!-- Orders List -->
            <div class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <div class="order-item p-6 border-l-4 hover:bg-gray-50 cursor-pointer transition"
                        data-order-id="{{ $order->id }}"
                        data-order='@json($order->load("items.product"))'
                        data-payment-status="{{ $order->payment_status }}"
                        data-created-date="{{ $order->created_at->timestamp }}"
                        onclick="selectOrder(this)"
                        style="border-left-color: {{ $loop->iteration % 2 == 0 ? '#3B82F6' : '#10B981' }}">
                        
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Customer Name -->
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($order->customer_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
                                    </div>
                                </div>

                                <!-- Order Items Description -->
                                <div class="ml-12 mt-2">
                                    <p class="text-sm text-gray-600">
                                        @if($order->items->count() > 0)
                                            @foreach($order->items->take(2) as $item)
                                                <span class="inline-block">{{ $item->product->name }}{{ !$loop->last ? ', ' : '' }}</span>
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <span class="text-gray-400">{{ '+' . ($order->items->count() - 2) . ' more' }}</span>
                                            @endif
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="text-right">
                                <p class="text-sm text-gray-500">TOTAL</p>
                                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No unpaid orders</p>
                        <p class="text-gray-400 text-sm mt-2">All orders have been settled!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: PAYMENT TERMINAL -->
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 sticky top-24">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <p class="text-gray-500 text-sm font-semibold">Active Selection</p>
                <h3 class="text-xl font-bold text-gray-800 mt-2" id="paymentTitle">Select an order</h3>
            </div>

            <!-- Payment Terminal Content -->
            <div class="p-6" id="paymentTerminal">
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-8">
                    <i class="fas fa-credit-card text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Select an order to begin payment</p>
                </div>

                <!-- Payment Form (Hidden by default) -->
                <div id="paymentForm" style="display: none;" class="space-y-4">
                    <!-- Customer Info -->
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">CUSTOMER NAME</p>
                        <p id="terminalCustomer" class="text-gray-800 font-semibold mt-1">-</p>
                    </div>

                    <!-- Order Items -->
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 max-h-24 overflow-y-auto">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">ORDER ITEMS</p>
                        <div id="terminalItems" class="space-y-1 text-sm">
                            <!-- Items will be inserted here -->
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="space-y-2 pt-3 border-t border-gray-100 bg-blue-50 p-4 rounded-xl mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span id="terminalSubtotal" class="font-semibold text-gray-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm" id="discountSummary" style="display: none;">
                            <span class="text-gray-600">Diskon</span>
                            <span id="terminalDiscountAmount" class="font-semibold text-red-600">- Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Pembayaran</span>
                            <span id="terminalTotalPrice" class="font-semibold text-gray-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm" id="dpSummary" style="display: none;">
                            <span class="text-gray-600">DP Diterima</span>
                            <span id="terminalDPAmount" class="font-semibold text-blue-600">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm" id="totalPaidSummary" style="display: none;">
                            <span class="text-gray-600">Total Terbayar</span>
                            <span id="terminalTotalPaid" class="font-semibold text-gray-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2 mt-2">
                            <span class="text-gray-800">Sisa Bayar</span>
                            <span id="terminalTotal" class="text-red-600">Rp 0</span>
                        </div>
                    </div>

                    <!-- Payment Type Tabs -->
                    <div class="space-y-3 mb-4" id="paymentTypeTabs">
                        <p class="text-xs text-gray-500 font-semibold">JENIS PEMBAYARAN</p>
                        <div class="grid grid-cols-3 gap-2" id="paymentTabsContainer">
                            <!-- Tabs akan di-generate oleh JavaScript berdasarkan payment status -->
                        </div>
                    </div>

                    <!-- Dynamic Content for Payment Type -->
                    <div id="paymentTypeContent" class="space-y-3">
                        <!-- Content akan di-generate oleh JS -->
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-4">
                        <form id="finalizeForm" method="POST" action="{{ route('kasir.pay') }}">
                            @csrf
                            <input type="hidden" id="selectedOrderId" name="order_id">
                            <input type="hidden" id="paymentMethod" name="method">
                            <input type="hidden" id="paymentAmount" name="amount">

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold transition flex items-center justify-center gap-2"
                                id="finalizeBtn" disabled>
                                <i class="fas fa-check-circle"></i> FINALIZE TRANSACTION
                            </button>
                        </form>

                        <button type="button" id="viewOrderButton" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-2 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2" onclick="showReceiptPopup()" disabled>
                            <i class="fas fa-file-pdf"></i> VIEW RECEIPT
                        </button>

                        <button type="button" id="cancelBtn" class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white py-2 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2" onclick="cancelOrder()" disabled>
                            <i class="fas fa-times-circle"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
            <div>
                <p class="text-lg font-bold text-white">Struk Pembayaran</p>
                <p class="text-sm text-blue-100">Preview struk pembayaran</p>
            </div>
            <button type="button" onclick="closeReceiptPopup()" class="text-white hover:text-blue-100 text-2xl transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh] bg-white" id="receiptContent"></div>
        <div class="flex gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
            <button type="button" onclick="printReceipt()" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                <i class="fas fa-print"></i> Print
            </button>
            <button type="button" onclick="closeReceiptPopup()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-3 rounded-lg font-semibold transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let selectedOrder = null;
let currentFilterType = 'unpaid'; // Default to unpaid
let currentSelectedElement = null; // Track the currently selected element

// Initialize time period dropdown value from URL parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const period = urlParams.get('period') || '7';
    const timePeriodSelect = document.getElementById('timePeriod');
    if (timePeriodSelect) {
        timePeriodSelect.value = period;
    }
    
    // Apply default filter to show only unpaid orders
    filterOrders('unpaid');
});

// Filter orders function
function filterOrders(filterType) {
    currentFilterType = filterType;
    
    // Map filter type to colors
    const colorMap = {
        'unpaid': { border: 'border-red-500', bg: 'bg-red-50', text: 'text-red-700' },
        'partial': { border: 'border-yellow-500', bg: 'bg-yellow-50', text: 'text-yellow-700' }
    };
    
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'border-red-500', 'border-yellow-500', 'bg-red-50', 'bg-yellow-50', 'text-red-700', 'text-yellow-700');
        btn.classList.add('border-gray-300', 'text-gray-700');
    });
    
    const activeBtn = document.querySelector(`[data-filter="${filterType}"]`);
    const colors = colorMap[filterType];
    activeBtn.classList.add('active', colors.border, colors.bg, colors.text);
    activeBtn.classList.remove('border-gray-300', 'text-gray-700');
    
    // Filter order items
    const orderItems = document.querySelectorAll('.order-item');
    orderItems.forEach(item => {
        const paymentStatus = item.dataset.paymentStatus;
        
        if (filterType === 'unpaid' && paymentStatus === 'unpaid') {
            item.style.display = '';
        } else if (filterType === 'partial' && paymentStatus === 'partial') {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// Generate payment tabs based on payment status
function generatePaymentTabs(paymentStatus) {
    const container = document.getElementById('paymentTabsContainer');
    container.innerHTML = '';
    
    const tabs = [];
    
    if (paymentStatus === 'partial') {
        // Already has DP, only show LUNASI
        tabs.push({
            type: 'pelunasan',
            icon: 'fas fa-check-circle',
            color: 'green',
            label: 'LUNASI'
        });
    } else if (paymentStatus === 'unpaid') {
        // Not paid yet, show only PENUH and DP (no LUNASI)
        tabs.push({
            type: 'full',
            icon: 'fas fa-bars',
            color: 'purple',
            label: 'PENUH'
        });
        tabs.push({
            type: 'dp',
            icon: 'fas fa-money-bill-wave',
            color: 'blue',
            label: 'DP'
        });
    }
    
    tabs.forEach((tab, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `payment-type-tab p-3 rounded-lg border-2 border-gray-200 hover:border-${tab.color}-500 hover:bg-${tab.color}-50 transition text-center ${idx === 0 ? 'active' : ''}`;
        btn.dataset.type = tab.type;
        if (idx === 0) {
            btn.classList.add(`border-${tab.color}-500`, `bg-${tab.color}-50`);
        }
        
        btn.innerHTML = `
            <i class="${tab.icon} text-xl text-${tab.color}-600 mb-1"></i>
            <p class="text-xs font-semibold text-gray-700">${tab.label}</p>
        `;
        
        btn.addEventListener('click', function() {
            selectPaymentType(this);
        });
        
        container.appendChild(btn);
    });
    
    // Set initial payment type based on tabs available
    currentPaymentType = tabs[0].type;
    renderPaymentForm(tabs[0].type);
    
    // Handle VIEW RECEIPT button visibility for initial tab
    const viewOrderButton = document.getElementById('viewOrderButton');
    if (tabs[0].type === 'dp') {
        viewOrderButton.style.display = 'none';
    } else {
        viewOrderButton.style.display = 'block';
    }
}

function selectPaymentType(btn) {
    // Remove previous selection
    document.querySelectorAll('.payment-type-tab').forEach(b => {
        b.classList.remove('border-purple-500', 'border-blue-500', 'border-green-500', 'bg-purple-50', 'bg-blue-50', 'bg-green-50');
    });

    // Add selection highlight
    const type = btn.dataset.type;
    const colorMap = {
        'full': 'purple',
        'dp': 'blue',
        'pelunasan': 'green'
    };
    const color = colorMap[type];
    btn.classList.add(`border-${color}-500`, `bg-${color}-50`);

    currentPaymentType = type;
    renderPaymentForm(type);
    
    // Hide VIEW RECEIPT button for DP payment type
    const viewOrderButton = document.getElementById('viewOrderButton');
    if (type === 'dp') {
        viewOrderButton.style.display = 'none';
    } else {
        viewOrderButton.style.display = 'block';
    }
}

function selectOrder(element) {
    // Remove previous selection highlight
    document.querySelectorAll('.order-item').forEach(el => {
        el.classList.remove('bg-blue-50', 'ring-2', 'ring-blue-400');
    });

    // Add highlight to clicked element
    element.classList.add('bg-blue-50', 'ring-2', 'ring-blue-400');
    
    // Store reference to currently selected element
    currentSelectedElement = element;

    // Get order data
    const orderId = element.dataset.orderId;
    const paymentStatus = element.dataset.paymentStatus;
    
    selectedOrder = {
        id: orderId,
        customer: element.querySelector('p:first-of-type').textContent.trim(),
        total: parseInt(element.querySelector('[data-order-id] p.text-2xl')?.textContent.match(/\d+/)?.[0]?.replace(/\./g, '') || 0)
    };

    // Show payment form and hide empty state
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('paymentForm').style.display = 'block';

    // Set order data
    selectedOrderData = JSON.parse(element.dataset.order);
    
    // Track currently selected order to prevent race condition from async calls
    currentSelectedOrderId = orderId;
    
    // Load payment detail from server
    loadOrderPaymentDetail(orderId);

    // Update payment terminal
    updatePaymentTerminal(element);

    // Set hidden inputs
    document.getElementById('selectedOrderId').value = orderId;
    document.getElementById('paymentAmount').value = selectedOrderData.total_price;
    document.getElementById('viewOrderButton').disabled = false;
    document.getElementById('cancelBtn').disabled = false;
    
    // Generate payment tabs based on payment status
    generatePaymentTabs(paymentStatus);
    
    // Note: updatePaymentSummary() will be called in loadOrderPaymentDetail() after payments are loaded
}

function updatePaymentTerminal(element) {
    const customerName = element.querySelector('p:first-of-type').textContent.trim();
    const totalText = element.querySelector('.text-2xl.font-bold').textContent;
    const totalAmount = parseInt(totalText.match(/[\d.]+/)[0].replace(/\./g, ''));

    // Update title
    document.getElementById('paymentTitle').textContent = customerName;

    // Update customer info
    document.getElementById('terminalCustomer').textContent = customerName;

    // Update items with quantity and price
    const orderData = JSON.parse(element.dataset.order);
    const itemsHtml = orderData.items.map(item => {
        const productName = item.product?.name || 'Item';
        const qty = item.quantity;
        const price = Number(item.price);
        const discount = Number(item.discount || 0);
        const finalPrice = Math.max(0, price - discount);
        const subtotal = Number(item.subtotal).toLocaleString('id-ID');
        
        const priceDisplay = discount > 0 
            ? `<span class="line-through text-red-500">Rp ${price.toLocaleString('id-ID')}</span><span class="text-red-600 ml-1">- Rp ${discount.toLocaleString('id-ID')}</span>`
            : `Rp ${price.toLocaleString('id-ID')}`;
        
        return `
            <div class="mb-2 pb-2 border-b border-gray-200 last:border-b-0">
                <div class="flex justify-between items-start gap-2 text-xs">
                    <span class="font-medium text-gray-800">${productName}</span>
                    <span class="font-semibold text-gray-800">Rp ${subtotal}</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">${qty} x ${priceDisplay}</div>
            </div>
        `;
    }).join('');
    document.getElementById('terminalItems').innerHTML = itemsHtml || '<p class="text-gray-700">Order items</p>';

    // Do NOT set terminalTotal here - let updatePaymentSummary() handle it after payments are loaded
}

// Load order payment detail from server
function loadOrderPaymentDetail(orderId) {
    fetch(`/kasir/payment/${orderId}/detail`)
        .then(response => response.json())
        .then(data => {
            // Only update if this is for the currently selected order (prevent race condition)
            if (currentSelectedOrderId !== orderId) {
                console.log(`Ignoring stale response for order ${orderId}, current order is ${currentSelectedOrderId}`);
                return;
            }
            
            if (data.success) {
                selectedOrderData.payments = data.data.payments;
                selectedOrderData.dp_amount = data.data.dp_amount;
                selectedOrderData.payment_status = data.data.payment_status;
                selectedOrderData.total_price = data.data.total_price; // Also update total_price from server
                updatePaymentSummary();
            }
        })
        .catch(error => console.error('Error:', error));
}


function showReceiptPopup() {
    if (!selectedOrderData) {
        return;
    }

    const modal = document.getElementById('receiptModal');
    const content = document.getElementById('receiptContent');

    const itemsHtml = selectedOrderData.items.map(item => {
        const productName = item.product?.name || 'Item';
        const qty = item.quantity;
        const price = Number(item.price);
        const discount = Number(item.discount || 0);
        const finalPrice = Math.max(0, price - discount);
        const subtotal = Number(item.subtotal).toLocaleString('id-ID');
        
        const priceDisplay = discount > 0 
            ? `<span class="line-through text-red-500">Rp ${price.toLocaleString('id-ID')}</span><span class="text-red-600 ml-1">- Rp ${discount.toLocaleString('id-ID')}</span>`
            : `Rp ${price.toLocaleString('id-ID')}`;

        return `
            <div class="flex justify-between items-start gap-2 mb-2">
                <div class="flex-1 text-sm">${productName}</div>
                <div class="text-sm text-right">Rp ${subtotal}</div>
            </div>
            <div class="flex justify-between text-xs text-gray-600 mb-3">
                <span>${qty} x ${priceDisplay}</span>
            </div>
        `;
    }).join('');

    const totalQty = selectedOrderData.items.reduce((sum, item) => sum + Number(item.quantity), 0);
    
    // Calculate subtotal before discount
    const discountAmount = Number(selectedOrderData.discount_amount || 0);
    const totalPrice = Number(selectedOrderData.total_price);
    const subtotal = totalPrice + discountAmount;
    
    // Payment info - prioritize dpAmount over payments
    const dpAmount = Number(selectedOrderData.dp_amount || 0);
    const paymentsPaidAmount = selectedOrderData.payments?.reduce((sum, p) => sum + Number(p.amount || 0), 0) || 0;
    const totalPaid = dpAmount > 0 ? dpAmount : paymentsPaidAmount;
    const remainingPayment = totalPrice - totalPaid;
    
    const method = selectedOrderData.payments?.length > 0
        ? selectedOrderData.payments[selectedOrderData.payments.length - 1].payment_method
        : 'cash';
    const paymentMethodLabel = method === 'transfer' ? 'Transfer' : 'Cash';
    
    // Determine if this is a pelunasan (has DP already paid)
    const isPelunasan = dpAmount > 0;
    
    // For payment display: use remainingPayment for sisa bayar, otherwise use last payment amount
    const displayPaymentAmount = isPelunasan ? remainingPayment : (
        selectedOrderData.payments?.length > 0
            ? Number(selectedOrderData.payments[selectedOrderData.payments.length - 1].amount)
            : remainingPayment
    );
    const paymentLabelText = isPelunasan ? 'Sisa Bayar' : `Bayar (${paymentMethodLabel})`;

    // Discount display
    const discountHtml = discountAmount > 0 ? `
        <div class="mb-2 flex justify-between text-sm"><span>Diskon</span><span class="font-semibold text-red-600">- Rp ${discountAmount.toLocaleString('id-ID')}</span></div>
    ` : '';
    
    // DP display
    const dpHtml = dpAmount > 0 ? `
        <div class="mb-2 flex justify-between text-sm"><span>DP Terbayar</span><span class="font-semibold text-blue-600">- Rp ${dpAmount.toLocaleString('id-ID')}</span></div>
    ` : '';

    content.innerHTML = `
        <div class="bg-white text-black font-sans leading-6">
            <div class="text-center mb-4">
                <p class="font-bold text-lg">Bukit Foto Studio Cab Km9</p>
                <p class="text-sm mt-1 text-gray-700">Jl. Kolonel H. Barlian, Kebun Bunga, Kec. Sukarami, Kota Palembang, Sumatera Selatan 30152</p>
                <p class="text-sm">Telp: 0851-5695-6302</p>
            </div>
            <div class="border-t-2 border-dashed border-gray-400 mb-4"></div>
            <div class="text-sm mb-4">
                <div class="flex justify-between mb-2"><span>${new Date(selectedOrderData.created_at).toISOString().split('T')[0]}</span><span>${new Date(selectedOrderData.created_at).toLocaleTimeString('id-ID')}</span></div>
                <div class="flex justify-between mb-2"><span>No.</span><span class="font-semibold">${selectedOrderData.order_code}</span></div>
                <div>Customer: <span class="font-medium">${selectedOrderData.customer_name}</span></div>
            </div>
            <div class="border-t-2 border-dashed border-gray-400 mb-4"></div>
            ${itemsHtml}
            <div class="border-t-2 border-dashed border-gray-400 mb-4"></div>
            <div class="mb-2 flex justify-between text-sm"><span>Total QTY</span><span class="font-semibold">${totalQty}</span></div>
            <div class="mb-2 flex justify-between text-sm"><span>Sub Total</span><span class="font-semibold">Rp ${subtotal.toLocaleString('id-ID')}</span></div>
            ${discountHtml}
            <div class="mb-3 flex justify-between text-base font-bold border-t-2 border-gray-300 pt-2"><span>Total</span><span>Rp ${totalPrice.toLocaleString('id-ID')}</span></div>
            ${dpHtml}
            <div class="mb-3 flex justify-between text-sm"><span>${paymentLabelText}</span><span class="font-semibold">Rp ${displayPaymentAmount.toLocaleString('id-ID')}</span></div>
            <div class="border-t-2 border-dashed border-gray-400 mb-4"></div>
            <div class="text-center text-sm"><p>Terima kasih telah berbelanja</p><p class="mt-2">Bukit Foto Studio Cab Km9</p></div>
        </div>
    `;
    modal.classList.remove('hidden');
}

function closeReceiptPopup() {
    document.getElementById('receiptModal').classList.add('hidden');
}

function printReceipt() {
    const printWindow = window.open('', '', 'height=600,width=400');
    
    const receiptHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Print - Struk Pembayaran</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Courier New', monospace;
                    width: 80mm;
                    margin: 0 auto;
                    padding: 5mm;
                }
                .receipt {
                    text-align: center;
                    font-size: 11pt;
                    line-height: 1.4;
                }
                .receipt-header {
                    margin-bottom: 10pt;
                    font-weight: bold;
                }
                .receipt-divider {
                    border-top: 2px dashed #000;
                    margin: 8pt 0;
                }
                .receipt-info {
                    text-align: left;
                    font-size: 10pt;
                    margin-bottom: 10pt;
                }
                .receipt-items {
                    margin-bottom: 10pt;
                    text-align: left;
                    font-size: 10pt;
                }
                .receipt-item {
                    margin-bottom: 5pt;
                }
                .receipt-item-name {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 2pt;
                }
                .receipt-item-detail {
                    font-size: 9pt;
                    color: #666;
                    display: flex;
                    justify-content: space-between;
                }
                .receipt-summary {
                    text-align: right;
                    font-size: 10pt;
                    margin-bottom: 10pt;
                }
                .receipt-summary-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 3pt;
                }
                .receipt-total {
                    font-size: 12pt;
                    font-weight: bold;
                    border-top: 2px solid #000;
                    padding-top: 5pt;
                    margin-top: 5pt;
                }
                .receipt-footer {
                    text-align: center;
                    font-size: 10pt;
                    margin-top: 10pt;
                }
                @media print {
                    body {
                        width: 80mm;
                        margin: 0;
                        padding: 2mm;
                    }
                    .receipt {
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                <div class="receipt-header">
                    <div>Bukit Foto Studio Cab Km9</div>
                    <div style="font-size: 10pt; font-weight: normal; margin-top: 3pt;">Jl. Kolonel H. Barlian, Kebun Bunga, Kec. Sukarami, Kota Palembang, Sumatera Selatan 30152</div>
                    <div style="font-size: 9pt; margin-top: 2pt;">Telp: 0851-5695-6302</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-info">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3pt;">
                        <span>${new Date(selectedOrderData.created_at).toISOString().split('T')[0]}</span>
                        <span>${new Date(selectedOrderData.created_at).toLocaleTimeString('id-ID')}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3pt;">
                        <span>No.</span>
                        <span>${selectedOrderData.order_code}</span>
                    </div>
                    <div>Customer: ${selectedOrderData.customer_name}</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-items">
                    ${selectedOrderData.items.map(item => {
                        const productName = item.product?.name || 'Item';
                        const qty = item.quantity;
                        const price = Number(item.price);
                        const discount = Number(item.discount || 0);
                        const finalPrice = Math.max(0, price - discount);
                        const subtotal = Number(item.subtotal).toLocaleString('id-ID');
                        
                        const priceDisplay = discount > 0 
                            ? `<span style="text-decoration: line-through; color: red;">Rp ${price.toLocaleString('id-ID')}</span><span style="color: red; margin-left: 5px;">- Rp ${discount.toLocaleString('id-ID')}</span>`
                            : `Rp ${price.toLocaleString('id-ID')}`;
                        
                        return `
                            <div class="receipt-item">
                                <div class="receipt-item-name">
                                    <span>${productName}</span>
                                    <span>Rp ${subtotal}</span>
                                </div>
                                <div class="receipt-item-detail">
                                    <span>${qty} x ${priceDisplay}</span>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-summary">
                    <div class="receipt-summary-row">
                        <span>Total QTY</span>
                        <span>${selectedOrderData.items.reduce((sum, item) => sum + Number(item.quantity), 0)}</span>
                    </div>
                    <div class="receipt-summary-row">
                        <span>Sub Total</span>
                        <span>Rp ${(() => {
                            const discountAmount = Number(selectedOrderData.discount_amount || 0);
                            const totalPrice = Number(selectedOrderData.total_price);
                            return (totalPrice + discountAmount).toLocaleString('id-ID');
                        })()}</span>
                    </div>
                    ${(() => {
                        const discountAmount = Number(selectedOrderData.discount_amount || 0);
                        return discountAmount > 0 ? `
                            <div class="receipt-summary-row">
                                <span>Diskon</span>
                                <span>- Rp ${discountAmount.toLocaleString('id-ID')}</span>
                            </div>
                        ` : '';
                    })()}
                    <div class="receipt-summary-row receipt-total">
                        <span>Total</span>
                        <span>Rp ${Number(selectedOrderData.total_price).toLocaleString('id-ID')}</span>
                    </div>
                    ${(() => {
                        const dpAmount = Number(selectedOrderData.dp_amount || 0);
                        return dpAmount > 0 ? `
                            <div class="receipt-summary-row" style="margin-top: 8pt;">
                                <span>DP Terbayar</span>
                                <span>- Rp ${dpAmount.toLocaleString('id-ID')}</span>
                            </div>
                        ` : '';
                    })()}
                    <div class="receipt-summary-row" style="margin-top: 8pt;">
                        <span>${(() => {
                            const dpAmount = Number(selectedOrderData.dp_amount || 0);
                            return dpAmount > 0 ? 'Sisa Bayar' : 'Bayar';
                        })()}</span>
                        <span>Rp ${(() => {
                            const totalPrice = Number(selectedOrderData.total_price);
                            const dpAmount = Number(selectedOrderData.dp_amount || 0);
                            const isPelunasan = dpAmount > 0;
                            
                            if (isPelunasan) {
                                // For pelunasan, calculate remaining payment
                                return (totalPrice - dpAmount).toLocaleString('id-ID');
                            } else {
                                // For full payment, use last payment amount or total
                                const lastPaymentAmount = selectedOrderData.payments?.length > 0
                                    ? Number(selectedOrderData.payments[selectedOrderData.payments.length - 1].amount)
                                    : totalPrice;
                                return lastPaymentAmount.toLocaleString('id-ID');
                            }
                        })()}</span>
                    </div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-footer">
                    <div>Terima kasih telah berbelanja</div>
                    <div style="margin-top: 5pt;">Bukit Foto Studio Cab Km9</div>
                </div>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(receiptHTML);
    printWindow.document.close();
    
    setTimeout(function() {
        printWindow.print();
    }, 250);
}

// Form validation and submission handler
document.getElementById('finalizeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const method = document.getElementById('paymentMethod').value;
    const orderId = document.getElementById('selectedOrderId').value;
    
    if (!method) {
        showNotification('Silahkan pilih metode pembayaran', 'warning');
        return false;
    }
    
    if (currentPaymentType === 'full') {
        // Submit to old payment form
        this.submit();
    } else if (currentPaymentType === 'dp') {
        const amount = parseInt(document.getElementById('dpAmountInput')?.value || 0);
        const dpAmount = parseInt(selectedOrderData.dp_amount || 0);
        const paymentsPaidAmount = selectedOrderData.payments?.reduce((sum, p) => sum + parseInt(p.amount || 0), 0) || 0;
        const totalPaid = dpAmount > 0 ? dpAmount : paymentsPaidAmount;
        const maxAmount = parseInt(selectedOrderData.total_price) || 0;
        const maxRemaining = maxAmount - totalPaid;
        if (amount <= 0 || amount > maxRemaining) {
            showNotification('Jumlah DP tidak valid', 'warning');
            return;
        }
        
        // Submit DP payment via AJAX
        submitDPPayment(orderId, amount, method);
    } else if (currentPaymentType === 'pelunasan') {
        const amount = parseInt(document.getElementById('pelunasanAmountInput')?.value || 0);
        const dpAmount = parseInt(selectedOrderData.dp_amount || 0);
        const paymentsPaidAmount = selectedOrderData.payments?.reduce((sum, p) => sum + parseInt(p.amount || 0), 0) || 0;
        const totalPaid = dpAmount > 0 ? dpAmount : paymentsPaidAmount;
        const maxAmount = parseInt(selectedOrderData.total_price) || 0;
        const maxRemaining = maxAmount - totalPaid;
        
        if (amount <= 0 || amount > maxRemaining) {
            showNotification('Jumlah pelunasan tidak valid', 'warning');
            return;
        }
        
        // Submit pelunasan payment via AJAX
        submitPelunasanPayment(orderId, amount, method);
    }
    
    return false;
});

function submitDPPayment(orderId, amount, method) {
    fetch('{{ route("kasir.payment.dp") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId,
            dp_amount: amount,
            payment_method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('DP berhasil dicatat!', 'success');
            // Refresh order list
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memproses pembayaran', 'error');
    });
}

function submitPelunasanPayment(orderId, amount, method) {
    fetch('{{ route("kasir.payment.pelunasan") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId,
            amount: amount,
            payment_method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Pelunasan berhasil dicatat!', 'success');
            // Refresh order list
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memproses pembayaran', 'error');
    });
}

// ============ NEW: Payment Type & DP System ============
let currentPaymentType = 'full';
let selectedOrderData = null;
let currentSelectedOrderId = null; // Track currently selected order to prevent race condition

// Payment type tab selection is now handled in generatePaymentTabs() and selectPaymentType()

// Initial render with 'full' type
function renderPaymentForm(type) {
    const content = document.getElementById('paymentTypeContent');
    const maxAmount = selectedOrderData ? parseInt(selectedOrderData.total_price) || 0 : 0;
    const dpAmount = selectedOrderData ? parseInt(selectedOrderData.dp_amount || 0) : 0;
    const paymentsPaidAmount = selectedOrderData ? (selectedOrderData.payments?.reduce((sum, p) => sum + parseInt(p.amount || 0), 0) || 0) : 0;
    // Total paid includes both DP and other payments
    const totalPaid = dpAmount > 0 ? dpAmount : paymentsPaidAmount;
    const remainingPayment = maxAmount - totalPaid;

    if (type === 'full') {
        content.innerHTML = `
            <div class="space-y-3">
                <p class="text-xs text-gray-500 font-semibold">METODE PEMBAYARAN</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" class="payment-method-btn p-3 rounded-lg border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 transition text-center"
                        data-method="cash">
                        <i class="fas fa-money-bill text-2xl text-green-600 mb-1"></i>
                        <p class="text-xs font-semibold text-gray-700">CASH</p>
                    </button>
                    <button type="button" class="payment-method-btn p-3 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition text-center"
                        data-method="transfer">
                        <i class="fas fa-university text-2xl text-blue-600 mb-1"></i>
                        <p class="text-xs font-semibold text-gray-700">TRANSFER</p>
                    </button>
                </div>
            </div>
        `;
        document.getElementById('finalizeBtn').textContent = '✓ Bayar Penuh';
        document.getElementById('paymentAmount').value = maxAmount;
        
        // Attach method selection
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                selectPaymentMethod(this);
            });
        });
        
        // Reset payment method and validate
        document.getElementById('paymentMethod').value = '';
        validatePaymentAmount();
    } else if (type === 'dp') {
        content.innerHTML = `
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-gray-500 font-semibold block mb-2">JUMLAH DP (Rp)</label>
                    <input type="number" id="dpAmountInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg" 
                           placeholder="Masukkan DP" step="1000" min="1000" max="${remainingPayment}">
                    <small class="text-gray-500">Maks: Rp ${remainingPayment.toLocaleString('id-ID')}</small>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold mb-2">METODE PEMBAYARAN</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="payment-method-btn p-3 rounded-lg border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 transition text-center"
                            data-method="cash">
                            <i class="fas fa-money-bill text-xl text-green-600 mb-1"></i>
                            <p class="text-xs font-semibold text-gray-700">CASH</p>
                        </button>
                        <button type="button" class="payment-method-btn p-3 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition text-center"
                            data-method="transfer">
                            <i class="fas fa-university text-xl text-blue-600 mb-1"></i>
                            <p class="text-xs font-semibold text-gray-700">TRANSFER</p>
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('finalizeBtn').textContent = '✓ Simpan DP';
        
        // Attach events
        document.getElementById('dpAmountInput').addEventListener('input', function() {
            document.getElementById('paymentAmount').value = this.value;
            validatePaymentAmount();
        });
        
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                selectPaymentMethod(this);
            });
        });
        
        // Reset payment method and validate
        document.getElementById('paymentMethod').value = '';
        validatePaymentAmount();
    } else if (type === 'pelunasan') {
        // Calculate remaining payment more carefully
        const remainingValue = remainingPayment > 0 ? remainingPayment : (() => {
            // Fallback calculation if remainingPayment is not positive
            const totalPrice = parseInt(selectedOrderData?.total_price || 0);
            const dpAmt = parseInt(selectedOrderData?.dp_amount || 0);
            const paidAmt = selectedOrderData?.payments?.reduce((sum, p) => sum + parseInt(p.amount || 0), 0) || 0;
            const actualPaid = dpAmt > 0 ? dpAmt : paidAmt;
            return Math.max(0, totalPrice - actualPaid);
        })();
        
        if (remainingValue <= 0) {
            // Order sudah fully paid
            content.innerHTML = `<div class="p-4 text-center text-green-600"><p>Order sudah lunas</p></div>`;
            document.getElementById('finalizeBtn').disabled = true;
            return;
        }
        
        content.innerHTML = `
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-gray-500 font-semibold block mb-2">JUMLAH PELUNASAN (Rp)</label>
                    <input type="hidden" id="pelunasanAmountInput" value="${remainingValue}">
                    <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
                        <p class="text-lg font-bold text-gray-800">Rp ${remainingValue.toLocaleString('id-ID')}</p>
                    </div>
                    <small class="text-gray-500">Otomatis: Sisa pembayaran penuh</small>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold mb-2">METODE PEMBAYARAN</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="payment-method-btn p-3 rounded-lg border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 transition text-center"
                            data-method="cash">
                            <i class="fas fa-money-bill text-xl text-green-600 mb-1"></i>
                            <p class="text-xs font-semibold text-gray-700">CASH</p>
                        </button>
                        <button type="button" class="payment-method-btn p-3 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition text-center"
                            data-method="transfer">
                            <i class="fas fa-university text-xl text-blue-600 mb-1"></i>
                            <p class="text-xs font-semibold text-gray-700">TRANSFER</p>
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('finalizeBtn').textContent = '✓ Bayar Pelunasan';
        document.getElementById('paymentAmount').value = remainingValue;
        
        // Attach events
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                selectPaymentMethod(this);
            });
        });
        
        // Reset payment method and validate
        document.getElementById('paymentMethod').value = '';
        validatePaymentAmount();
    }
}

function selectPaymentMethod(btn) {
    document.querySelectorAll('.payment-method-btn').forEach(b => {
        b.classList.remove('border-green-500', 'border-blue-500', 'bg-green-50', 'bg-blue-50');
    });
    
    const method = btn.dataset.method;
    const color = method === 'cash' ? 'green' : 'blue';
    btn.classList.add(`border-${color}-500`, `bg-${color}-50`);
    
    document.getElementById('paymentMethod').value = method;
    
    // Enable button immediately for full and pelunasan
    const finalizeBtn = document.getElementById('finalizeBtn');
    if (currentPaymentType === 'full' || currentPaymentType === 'pelunasan') {
        finalizeBtn.disabled = false;
    } else {
        // For DP, validate amount
        validatePaymentAmount();
    }
}

function validatePaymentAmount() {
    const method = document.getElementById('paymentMethod').value;
    const finalizeBtn = document.getElementById('finalizeBtn');
    
    if (!method) {
        finalizeBtn.disabled = true;
        return;
    }
    
    if (currentPaymentType === 'full') {
        finalizeBtn.disabled = false;
    } else if (currentPaymentType === 'dp') {
        const amount = parseInt(document.getElementById('dpAmountInput')?.value || 0);
        const dpAmount = parseInt(selectedOrderData.dp_amount || 0);
        const paymentsPaidAmount = selectedOrderData.payments?.reduce((sum, p) => sum + parseInt(p.amount || 0), 0) || 0;
        const totalPaid = dpAmount > 0 ? dpAmount : paymentsPaidAmount;
        const maxAmount = parseInt(selectedOrderData.total_price) || 0;
        const maxRemaining = maxAmount - totalPaid;
        finalizeBtn.disabled = amount <= 0 || amount > maxRemaining;
    } else if (currentPaymentType === 'pelunasan') {
        const pelunasanInput = document.getElementById('pelunasanAmountInput');
        if (!pelunasanInput) {
            // Element not found yet, disable button
            finalizeBtn.disabled = true;
            return;
        }
        const amount = parseInt(pelunasanInput.value || 0);
        // For pelunasan, just check if amount is positive
        finalizeBtn.disabled = amount <= 0 || !method;
    }
}

// Update payment summary
function updatePaymentSummary() {
    if (!selectedOrderData) return;
    
    // Ensure all values are properly converted to integers
    const discountAmount = parseInt(selectedOrderData.discount_amount || 0);
    const totalPrice = parseInt(selectedOrderData.total_price) || 0;
    const subtotal = totalPrice + discountAmount; // Reconstruct subtotal from total_price
    const dpAmount = parseInt(selectedOrderData.dp_amount || 0);
    const paymentsPaidAmount = selectedOrderData.payments?.reduce((sum, p) => sum + parseInt(p.amount || 0), 0) || 0;
    const totalPaid = dpAmount > 0 ? dpAmount : paymentsPaidAmount;
    const remaining = totalPrice - totalPaid;
    
    // Show subtotal
    document.getElementById('terminalSubtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
    
    // Show discount if exists
    if (discountAmount > 0) {
        document.getElementById('discountSummary').style.display = 'flex';
        document.getElementById('terminalDiscountAmount').textContent = `- Rp ${discountAmount.toLocaleString('id-ID')}`;
    } else {
        document.getElementById('discountSummary').style.display = 'none';
    }
    
    // Show total after discount
    document.getElementById('terminalTotalPrice').textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
    
    if (dpAmount > 0) {
        document.getElementById('dpSummary').style.display = 'flex';
        document.getElementById('terminalDPAmount').textContent = `Rp ${dpAmount.toLocaleString('id-ID')}`;
    }
    
    document.getElementById('totalPaidSummary').style.display = 'none';
    document.getElementById('terminalTotal').textContent = `Rp ${remaining.toLocaleString('id-ID')}`;
}

// Cancel Order function
function cancelOrder() {
    // Show confirmation dialog
    showConfirmModal(
        'Hapus Order',
        'Apakah Anda yakin ingin menghapus order ini? Tindakan ini tidak dapat dibatalkan.',
        'fas fa-trash-alt text-red-600',
        'Hapus',
        deleteOrderConfirmed
    );
}

function deleteOrderConfirmed() {
    const orderId = document.getElementById('selectedOrderId').value;
    
    if (!orderId) {
        return;
    }

    // Delete order via API
    fetch(`/kasir/orders/${orderId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the order from the list
            if (currentSelectedElement) {
                currentSelectedElement.remove();
            }

            // Reset UI
            selectedOrder = null;
            selectedOrderData = null;
            currentSelectedOrderId = null;
            currentSelectedElement = null;

            // Hide payment form and show empty state
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('paymentForm').style.display = 'none';

            // Reset title
            document.getElementById('paymentTitle').textContent = 'Select an order';

            // Disable buttons
            document.getElementById('finalizeBtn').disabled = true;
            document.getElementById('viewOrderButton').disabled = true;
            document.getElementById('cancelBtn').disabled = true;

            // Clear form inputs
            document.getElementById('selectedOrderId').value = '';
            document.getElementById('paymentAmount').value = '';
            document.getElementById('paymentMethod').value = '';

            showNotification('Order berhasil dihapus!', 'success');
            
            // Update pending orders counter
            const pendingBadge = document.querySelector('.bg-green-100.text-green-700');
            if (pendingBadge) {
                const currentCount = parseInt(pendingBadge.textContent.match(/\d+/)[0]);
                const newCount = Math.max(0, currentCount - 1);
                pendingBadge.textContent = `${newCount} PENDING`;
            }
        } else {
            showNotification('Error: ' + (data.message || 'Gagal menghapus order'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menghapus order', 'error');
    });
}

// Time period filter functionality
function applyTimePeriodFilter() {
    const timePeriod = document.getElementById('timePeriod').value;
    
    // Create new URL with parameters
    const url = new URL(window.location);
    url.searchParams.set('period', timePeriod);
    
    window.location = url.toString();
}

</script>

<style>
/* Tail color mapping helper */
@supports selector(:has(*)) {
    .border-green-500 { border-color: rgb(34, 197, 94); }
    .border-blue-500 { border-color: rgb(59, 130, 246); }
    .border-purple-500 { border-color: rgb(168, 85, 247); }
    .border-orange-500 { border-color: rgb(249, 115, 22); }
    .bg-green-50 { background-color: rgb(240, 253, 244); }
    .bg-blue-50 { background-color: rgb(239, 246, 255); }
    .bg-purple-50 { background-color: rgb(250, 245, 255); }
    .bg-orange-50 { background-color: rgb(255, 247, 237); }
}

#receiptModal {
    display: flex !important;
}

#receiptModal.hidden {
    display: none !important;
}

.receipt {
    white-space: pre-wrap;
    word-wrap: break-word;
}

@media print {
    #receiptModal {
        position: static;
        background: white;
    }
    .receipt {
        max-width: 220px;
        margin: 0 auto;
    }
}
</style>

@endsection