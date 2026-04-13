@extends('layouts.app')

@section('title', 'Create Order - Studio Presisi')
@section('page-title', 'Buat Pesanan Baru')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    
    <div class="w-full lg:w-[60%]">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-th-large text-blue-600"></i> Katalog Produk
                </h2>
            </div>

            <div class="p-6">
                <div class="flex flex-wrap gap-2 mb-6 pb-4 border-b border-gray-100">
                    <button type="button" class="category-filter px-4 py-2 rounded-xl text-sm font-bold bg-blue-600 text-white transition-all active:scale-95 shadow-md shadow-blue-100"
                        data-category="all">
                        ALL
                    </button>
                    <button type="button" class="category-filter px-4 py-2 rounded-xl text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all active:scale-95"
                        data-category="cetak">
                        CETAK
                    </button>
                    <button type="button" class="category-filter px-4 py-2 rounded-xl text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all active:scale-95"
                        data-category="studio">
                        STUDIO
                    </button>
                    <button type="button" class="category-filter px-4 py-2 rounded-xl text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all active:scale-95"
                        data-category="barang">
                        BARANG
                    </button>
                </div>

                <div class="mb-4 relative">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input 
                            type="text" 
                            id="productSearch"
                            placeholder="Cari produk..."
                            class="w-full pl-12 pr-4 py-3 bg-gradient-to-r from-white-50 to-indigo-50 border border-black-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all shadow-sm hover:border-blue-300"
                        >
                    </div>
                </div>

                <div class="max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-3 gap-4">
                        @forelse($products as $product)
                            @php
                                $categoryType = in_array(strtolower($product->category ?? ''), ['print', 'cetak']) ? 'cetak' : (in_array(strtolower($product->category ?? ''), ['goods', 'barang']) ? 'barang' : (strtolower($product->category ?? '') === 'studio' ? 'studio' : 'all'));
                                
                                // Icon configuration by category
                                $iconConfig = [
                                    'cetak' => ['icon' => 'fa-print', 'bg' => 'from-purple-100 to-pink-100', 'text' => 'text-purple-500'],
                                    'studio' => ['icon' => 'fa-camera', 'bg' => 'from-yellow-100 to-orange-100', 'text' => 'text-yellow-600'],
                                    'barang' => ['icon' => 'fa-shopping-bag', 'bg' => 'from-green-100 to-emerald-100', 'text' => 'text-green-600'],
                                    'all' => ['icon' => 'fa-box', 'bg' => 'from-blue-100 to-indigo-100', 'text' => 'text-blue-400']
                                ];
                                $config = $iconConfig[$categoryType] ?? $iconConfig['all'];
                            @endphp
                            <div class="product-item flex flex-col p-4 border border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/30 transition-all group"
                                data-category="{{ $categoryType }}"
                                data-product-id="{{ $product->id }}">
                                
                                <div class="mb-3">
                                    <div class="h-20 bg-gradient-to-br {{ $config['bg'] }} rounded-lg flex items-center justify-center mb-2">
                                        <i class="fas {{ $config['icon'] }} text-2xl {{ $config['text'] }}"></i>
                                    </div>
                                    <p class="font-bold text-gray-800 group-hover:text-blue-700 transition-colors text-sm line-clamp-2">{{ $product->name }}</p>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                        @php
                                            $categoryLabel = strtoupper($product->category ?? 'N/A');
                                            if (in_array(strtolower($product->category ?? ''), ['print', 'cetak'])) {
                                                $categoryLabel = 'Cetak';
                                            } elseif (strtolower($product->category ?? '') === 'studio') {
                                                $categoryLabel = 'Studio';
                                            } elseif (in_array(strtolower($product->category ?? ''), ['goods', 'barang'])) {
                                                $categoryLabel = 'Barang';
                                            }
                                        @endphp
                                        {{ $categoryLabel }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 mt-auto">
                                    <div>
                                        <p class="font-black text-gray-900 price-label text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    </div>
                                    <button type="button" class="add-to-cart bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-100 w-full"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-product-consumer-price="{{ $product->price }}"
                                        data-product-amateur-price="{{ $product->amatir_price ?? $product->price }}">
                                        <i class="fas fa-plus"></i> ADD
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-12 text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <i class="fas fa-box-open text-4xl mb-3"></i>
                                <p class="font-medium">No products available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-[40%]">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 sticky top-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-green-600"></i> Pesanan Anda
                </h2>
            </div>

            <form id="orderForm" action="{{ route('penerima.orders.store') }}" method="POST" class="p-4">
                @csrf

                <div class="mb-6">
                    <div class="max-h-64 overflow-y-auto space-y-3 mb-3 border border-gray-100 bg-gray-50 rounded-xl p-3 custom-scrollbar">
                        <div id="orderItems">
                            <div class="text-center py-8">
                                <i class="fas fa-cart-arrow-down text-gray-300 text-3xl mb-2"></i>
                                <p class="text-gray-400 text-sm italic">Belum ada item ditambahkan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="itemsInput" name="items" value="[]">
                <input type="hidden" id="discountAmountInput" name="discount_amount" value="0">
                <input type="hidden" id="subtotalInput" name="subtotal" value="0">

                <div class="space-y-3 mb-4">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Nama Konsumen / No. Nota</label>
                        <input type="text" name="customer_name" id="customerName"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="Isi Nama atau Nomor Nota" required>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Nomor Telepon</label>
                        <input type="tel" name="customer_phone" id="customerPhone"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="flex items-center gap-3 p-2.5 bg-blue-50 rounded-xl border border-blue-100">
                        <input type="checkbox" id="isAmateur" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                        <label for="isAmateur" class="text-sm font-bold text-blue-800 cursor-pointer">Gunakan Harga Amatir</label>
                    </div>
                </div>

                <div class="space-y-2 pt-3 border-t border-gray-100 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">Subtotal</span>
                        <span id="subtotal" class="font-bold text-gray-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">Total Diskon</span>
                        <span id="discount" class="font-bold text-red-500">- Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center pt-1.5">
                        <span class="text-gray-800 font-black uppercase text-xs">Grand Total</span>
                        <span id="grandTotal" class="text-xl font-black text-green-600">Rp 0</span>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-200 disabled:text-gray-400 text-white py-3 rounded-xl font-black text-base shadow-lg shadow-green-100 transition-all flex items-center justify-center gap-3 active:scale-[0.98]" disabled>
                    <i class="fas fa-check-circle"></i> SUBMIT ORDER
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Elegant Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { 
        background: linear-gradient(180deg, rgba(148, 163, 184, 0.4) 0%, rgba(100, 116, 139, 0.4) 100%);
        border-radius: 10px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { 
        background: linear-gradient(180deg, rgba(148, 163, 184, 0.6) 0%, rgba(100, 116, 139, 0.6) 100%);
        background-clip: padding-box;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:active { 
        background: linear-gradient(180deg, rgba(71, 85, 105, 0.8) 0%, rgba(51, 65, 85, 0.8) 100%);
        background-clip: padding-box;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi State
    const orderItems = {};
    let isAmateur = false;

    // 2. Fungsi Filter Kategori (LENGKAP)
    const categoryButtons = document.querySelectorAll('.category-filter');
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedCategory = this.dataset.category;

            // Reset Styles
            categoryButtons.forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-100');
                b.classList.add('text-gray-600', 'bg-white', 'border', 'border-gray-200');
            });

            // Active Style
            this.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-100');
            this.classList.remove('text-gray-600', 'bg-white', 'border', 'border-gray-200');

            // Filter Action
            applyProductFilters(selectedCategory);
        });
    });

    // 2a. Fungsi Gabungan Filter & Search
    function applyProductFilters(selectedCategory = null) {
        const searchTerm = document.getElementById('productSearch')?.value.toLowerCase() || '';
        const activeCategory = selectedCategory || document.querySelector('.category-filter.bg-blue-600')?.dataset.category || 'all';
        
        const products = document.querySelectorAll('.product-item');
        products.forEach(item => {
            const itemCategory = item.dataset.category;
            const productName = item.querySelector('p:nth-of-type(1)')?.textContent?.toLowerCase() || '';
            
            // Check category filter
            const categoryMatch = activeCategory === 'all' || itemCategory === activeCategory;
            
            // Check search filter
            const searchMatch = searchTerm === '' || productName.includes(searchTerm);
            
            // Show/hide based on both filters
            if (categoryMatch && searchMatch) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // 2b. Event Listener untuk Search Input
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            applyProductFilters();
        });
    }

    // 3. Fungsi Tambah ke Keranjang (LENGKAP)
    const addButtons = document.querySelectorAll('.add-to-cart');
    addButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const consumerPrice = parseFloat(this.dataset.productConsumerPrice);
            const amateurPrice = parseFloat(this.dataset.productAmateurPrice);
            
            // Tentukan harga berdasarkan status checkbox amatir
            const currentPrice = isAmateur ? amateurPrice : consumerPrice;

            if (orderItems[productId]) {
                orderItems[productId].quantity++;
                orderItems[productId].price = currentPrice;
            } else {
                orderItems[productId] = {
                    product_id: productId,
                    name: productName,
                    consumer_price: consumerPrice,
                    amatir_price: amateurPrice,
                    price: currentPrice,
                    quantity: 1,
                    discount: 0
                };
            }

            updateOrderDisplay();
        });
    });

    // 4. Render Tampilan List Pesanan (LENGKAP TANPA SINGKATAN)
    function updateOrderDisplay() {
        const itemsContainer = document.getElementById('orderItems');
        const itemsArray = Object.values(orderItems);

        if (itemsArray.length === 0) {
            itemsContainer.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-cart-arrow-down text-gray-300 text-3xl mb-2"></i>
                    <p class="text-gray-400 text-sm italic">Belum ada item ditambahkan</p>
                </div>`;
        } else {
            let htmlContent = '';
            itemsArray.forEach(item => {
                const discountedPrice = Math.max(0, (item.price || 0) - (item.discount || 0));
                const itemTotal = discountedPrice * (item.quantity || 1);
                
                htmlContent += `
                    <div class="bg-white border border-gray-200 rounded-xl p-3 mb-2 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex-1 pr-2">
                                <p class="text-base font-bold text-gray-800 truncate">${item.name}</p>
                                <p class="text-xs text-gray-500 font-medium">
                                    Hrg: Rp ${(item.price || 0).toLocaleString('id-ID')}
                                    ${item.discount > 0 ? ` <span class="text-red-500">| Disc: Rp ${(item.discount || 0).toLocaleString('id-ID')}</span>` : ''}
                                </p>
                            </div>
                            <button type="button" class="text-red-400 hover:text-red-600 transition-colors"
                                onclick="removeItem('${item.product_id}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        
                        <div class="flex gap-2 items-end">
                            <div class="w-24">
                                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Qty</label>
                                <div class="flex items-center bg-gray-100 rounded-lg px-1 py-0.5">
                                    <button type="button" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-xs" 
                                        onclick="decreaseQty('${item.product_id}')">
                                        <i class="fas fa-minus text-[8px]"></i>
                                    </button>
                                    <input type="number" class="w-full bg-transparent text-center text-xs font-bold outline-none" 
                                        value="${item.quantity}" min="1" onchange="updateQtyFromInput(this)" data-product-id="${item.product_id}">
                                    <button type="button" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-xs" 
                                        onclick="increaseQty('${item.product_id}')">
                                        <i class="fas fa-plus text-[8px]"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Diskon (Rp)</label>
                                <input type="number" class="w-full bg-gray-100 rounded-lg px-2 py-1 text-sm font-bold outline-none border border-transparent focus:border-orange-300" 
                                    value="${item.discount || 0}" min="0" onchange="updateDiscount(this)" data-product-id="${item.product_id}">
                            </div>

                            <div class="text-right">
                                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Total</label>
                                <p class="text-sm font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">
                                    Rp ${itemTotal.toLocaleString('id-ID')}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
            itemsContainer.innerHTML = htmlContent;
        }

        updateSummary();
    }

    // 5. Fungsi Kontrol Quantity & Item (LENGKAP)
    window.increaseQty = function(productId) {
        if (orderItems[productId]) {
            orderItems[productId].quantity++;
            updateOrderDisplay();
        }
    };

    window.decreaseQty = function(productId) {
        if (orderItems[productId] && orderItems[productId].quantity > 1) {
            orderItems[productId].quantity--;
            updateOrderDisplay();
        }
    };

    window.removeItem = function(productId) {
        delete orderItems[productId];
        updateOrderDisplay();
    };

    window.updateQtyFromInput = function(input) {
        const productId = input.dataset.productId;
        let val = parseInt(input.value) || 1;
        if (val < 1) val = 1;
        if (orderItems[productId]) {
            orderItems[productId].quantity = val;
            updateOrderDisplay();
        }
    };

    window.updateDiscount = function(input) {
        const productId = input.dataset.productId;
        let discount = parseFloat(input.value) || 0;
        if (discount < 0) discount = 0;
        
        if (orderItems[productId]) {
            const maxPrice = orderItems[productId].price;
            if (discount > maxPrice) {
                discount = maxPrice;
                input.value = maxPrice;
            }
            orderItems[productId].discount = discount;
            updateOrderDisplay();
        }
    };

    // 6. Fungsi Kalkulasi Akhir (LENGKAP)
    function updateSummary() {
        const itemsArray = Object.values(orderItems);
        let subtotal = 0;
        let totalDiscount = 0;

        itemsArray.forEach(item => {
            const basePrice = item.price || 0;
            const qty = item.quantity || 1;
            const discPerItem = item.discount || 0;

            subtotal += (basePrice - discPerItem) * qty;
            totalDiscount += (discPerItem * qty);
        });

        // Tampilkan di UI
        document.getElementById('subtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
        document.getElementById('discount').textContent = `- Rp ${totalDiscount.toLocaleString('id-ID')}`;
        document.getElementById('grandTotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;

        // Masukkan ke Hidden Input untuk dikirim ke Controller
        const itemsJson = itemsArray.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            price: item.price,
            discount: item.discount || 0,
            final_price: (item.price - item.discount),
            price_type: isAmateur ? 'amatir' : 'konsumen'
        }));

        document.getElementById('itemsInput').value = JSON.stringify(itemsJson);
        document.getElementById('discountAmountInput').value = totalDiscount;
        document.getElementById('subtotalInput').value = subtotal;

        // Validasi tombol submit
        document.getElementById('submitBtn').disabled = itemsArray.length === 0;
    }

    // 7. Event Handler Checkbox Amatir (LENGKAP)
    document.getElementById('isAmateur').addEventListener('change', function() {
        isAmateur = this.checked;

        // Update harga di keranjang
        Object.values(orderItems).forEach(item => {
            item.price = isAmateur ? item.amatir_price : item.consumer_price;
        });

        // Update label harga di katalog
        document.querySelectorAll('.product-item').forEach(item => {
            const priceLabel = item.querySelector('.price-label');
            const btn = item.querySelector('.add-to-cart');
            if (priceLabel && btn) {
                const price = isAmateur ? 
                    parseFloat(btn.dataset.productAmateurPrice) : 
                    parseFloat(btn.dataset.productConsumerPrice);
                priceLabel.textContent = `Rp ${price.toLocaleString('id-ID')}`;
            }
        });

        updateOrderDisplay();
    });

    // 8. Validasi Form Sebelum Kirim (LENGKAP)
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        const items = Object.values(orderItems);
        if (items.length === 0) {
            e.preventDefault();
            alert('Silahkan pilih produk terlebih dahulu!');
            return;
        }

        const name = document.getElementById('customerName').value;
        if (!name) {
            e.preventDefault();
            alert('Nama Konsumen harus diisi!');
            return;
        }
    });

    // Jalankan kalkulasi awal
    updateSummary();
});
</script>
@endsection