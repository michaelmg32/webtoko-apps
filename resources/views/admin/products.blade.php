@extends('layouts.app')

@section('title', 'Product Management - Admin')
@section('page-title', 'Product Management')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="w-full lg:max-w-2xl">
                <label for="productSearch" class="sr-only">Search products</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <i class="fas fa-search"></i>
                    </span>
                    <input id="productSearch" type="text" 
                        placeholder="Cari nama produk, SKU, atau kategori..."
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all outline-none text-gray-700" />
                </div>
            </div>

            <a href="{{ route('admin.products.create') }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 active:scale-95 whitespace-nowrap">
                <i class="fas fa-plus-circle text-lg"></i> 
                <span>Tambah Produk</span>
            </a>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Filter Kategori:</span>
            <button type="button" class="category-tab px-5 py-2 rounded-full font-bold text-sm transition-all border {{ $activeClass = 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20' }}"
                data-category="all">
                Semua
            </button>
            <button type="button" class="category-tab px-5 py-2 rounded-full font-bold text-sm transition-all border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 bg-white"
                data-category="cetak">
                Cetak
            </button>
            <button type="button" class="category-tab px-5 py-2 rounded-full font-bold text-sm transition-all border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 bg-white"
                data-category="studio">
                Studio
            </button>
            <button type="button" class="category-tab px-5 py-2 rounded-full font-bold text-sm transition-all border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 bg-white"
                data-category="barang">
                Barang
            </button>
        </div>
    </div>

    <div id="productListContainer" class="flex flex-col gap-3">
        @forelse($products as $product)
            @php
                $categoryKey = in_array(strtolower($product->category), ['print', 'cetak']) ? 'cetak' : (in_array(strtolower($product->category), ['goods', 'barang']) ? 'barang' : strtolower($product->category));
                $categoryBg = match($categoryKey) {
                    'cetak' => 'from-blue-600 to-blue-700',
                    'barang' => 'from-orange-600 to-orange-700',
                    'studio' => 'from-purple-600 to-purple-700',
                    default => 'from-gray-600 to-gray-700'
                };
                $categoryIcon = match($categoryKey) {
                    'cetak' => 'fa-print',
                    'barang' => 'fa-shopping-bag',
                    'studio' => 'fa-camera',
                    default => 'fa-box'
                };
                $categoryLabel = match($categoryKey) {
                    'cetak' => 'Cetak',
                    'barang' => 'Barang',
                    'studio' => 'Studio',
                    default => 'Lainnya'
                };
            @endphp
            
            <div class="product-row bg-white rounded-xl border border-gray-200 hover:shadow-md transition-all duration-300 group flex flex-col sm:flex-row items-center p-4 gap-4"
                data-product-id="{{ $product->id }}"
                data-category="{{ $categoryKey }}"
                data-name="{{ strtolower($product->name) }}"
                data-sku="{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}">
                
                <!-- Icon/Avatar -->
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br {{ $categoryBg }} flex items-center justify-center flex-shrink-0 relative overflow-hidden shadow-sm">
                    <i class="fas {{ $categoryIcon }} text-2xl text-white/90 group-hover:scale-110 transition-transform"></i>
                    
                    <!-- Stock Indicator Dot -->
                    <div class="absolute bottom-1.5 right-1.5 w-2.5 h-2.5 rounded-full border-2 border-white {{ $product->unlimited_stock ? 'bg-green-500' : ($product->stock > 0 ? 'bg-yellow-500' : 'bg-red-500') }}" title="Stock: {{ $product->unlimited_stock ? '∞' : $product->stock ?? 0 }}"></div>
                </div>

                <!-- Product Details -->
                <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 w-full">
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-base truncate group-hover:text-blue-600 transition-colors product-name">
                            {{ $product->name }}
                        </h4>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs text-gray-500 font-mono font-bold product-sku">SKU: {{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="bg-gray-100 text-gray-600 text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wider">{{ $categoryLabel }}</span>
                            <span class="text-[10px] font-bold {{ $product->unlimited_stock ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $product->unlimited_stock ? 'Stok Tidak Terbatas' : 'Stok: ' . ($product->stock ?? 0) }}
                            </span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="sm:text-right mt-2 sm:mt-0 bg-gray-50 sm:bg-transparent p-2 sm:p-0 rounded-lg">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5 hidden sm:block">Harga Utama</p>
                        <p class="font-black text-base text-gray-900 whitespace-nowrap">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 mt-3 sm:mt-0 w-full sm:w-auto justify-end pt-3 sm:pt-0 border-t border-gray-100 sm:border-0">
                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                       class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-95" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" 
                       class="flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-95" 
                       onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')"
                       title="Hapus Produk">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                    <i class="fas fa-box-open text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                <p class="text-gray-500 mb-8 max-w-xs mx-auto">Mungkin kamu bisa mencoba kata kunci lain atau kategori yang berbeda.</p>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-black transition-all">
                    <i class="fas fa-plus"></i> Buat Produk Baru
                </a>
            </div>
        @endforelse
    </div>

    <!-- Load More Button -->
    <div id="loadMoreContainer" class="mt-12 flex justify-center">
        <button id="loadMoreBtn" type="button" 
            class="inline-flex items-center gap-2 px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg active:scale-95">
            <i class="fas fa-chevron-down"></i>
            <span>Muat Lebih Banyak</span>
        </button>
    </div>

    <!-- Product Count -->
    <div class="mt-2 text-center">
        <p id="productCount" class="text-sm font-medium text-gray-600"></p>
    </div>
</div>

@php
// Prepare ALL products data for JavaScript (untuk search di semua produk, bukan hanya yang di-paginate)
$productsData = [];
foreach($allProducts as $p) {
    $cat = in_array(strtolower($p->category), ['print', 'cetak']) ? 'cetak' : 
           (in_array(strtolower($p->category), ['goods', 'barang']) ? 'barang' : strtolower($p->category));
    $productsData[] = [
        'id' => $p->id,
        'name' => strtolower($p->name),
        'sku' => str_pad($p->id, 4, '0', STR_PAD_LEFT),
        'category' => $cat
    ];
}
@endphp

<script>
// Infinite Scroll Logic with Full Search
const itemsPerLoad = 15;
let itemsShown = itemsPerLoad;
const productSearchInput = document.getElementById('productSearch');
const categoryTabs = document.querySelectorAll('.category-tab');
const productCount = document.getElementById('productCount');
const loadMoreBtn = document.getElementById('loadMoreBtn');
const loadMoreContainer = document.getElementById('loadMoreContainer');
let selectedCategory = 'all';
let isLoading = false;

// Store all products data
const allProductsData = @json($productsData);

// Hasil filter dari search
let filteredProductIds = allProductsData.map(p => p.id);

function updateVisibility() {
    const allRows = document.querySelectorAll('.product-row');
    let visibleCount = 0;
    const totalVisible = filteredProductIds.length;

    // Update visibility berdasarkan itemsShown
    allRows.forEach(row => {
        const productId = parseInt(row.dataset.productId || '0');
        const isInFilteredResults = filteredProductIds.includes(productId);
        
        if (!isInFilteredResults) {
            row.style.display = 'none';
        } else {
            // Hitung index di filtered results
            const visibleIndex = filteredProductIds.indexOf(productId) + 1;
            if (visibleIndex <= itemsShown) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
    });

    // Update product count
    if (totalVisible === 0) {
        productCount.textContent = 'Tidak ada produk yang sesuai';
    } else {
        productCount.textContent = `Menampilkan ${visibleCount} dari ${totalVisible} produk`;
    }

    // Sembunyikan tombol Load More jika sudah semua
    if (visibleCount >= totalVisible || totalVisible === 0) {
        loadMoreContainer.style.display = 'none';
    } else {
        loadMoreContainer.style.display = 'flex';
    }
}

function filterProducts() {
    const searchTerm = productSearchInput?.value.toLowerCase() || '';
    itemsShown = itemsPerLoad; // Reset ke 15 setiap kali search

    // Filter dengan data, bukan DOM
    if (!searchTerm) {
        // Jika kosong, tampilkan semua sesuai kategori
        filteredProductIds = allProductsData
            .filter(p => selectedCategory === 'all' || p.category === selectedCategory)
            .map(p => p.id);
    } else {
        // Search di semua produk data
        filteredProductIds = allProductsData
            .filter(p => {
                const matchesSearch = p.name.includes(searchTerm) || p.sku.toLowerCase().includes(searchTerm) || p.category.includes(searchTerm);
                const matchesCategory = selectedCategory === 'all' || p.category === selectedCategory;
                return matchesSearch && matchesCategory;
            })
            .map(p => p.id);
    }

    updateVisibility();
}

function loadMoreItems() {
    if (isLoading) return;
    isLoading = true;
    
    // Simulate loading delay
    setTimeout(() => {
        itemsShown += itemsPerLoad;
        updateVisibility();
        isLoading = false;
    }, 300);
}

// Event listener untuk tombol Load More
loadMoreBtn?.addEventListener('click', loadMoreItems);

productSearchInput?.addEventListener('input', filterProducts);

categoryTabs.forEach(tab => {
    tab.addEventListener('click', function() {
        selectedCategory = this.dataset.category;

        // Styling tab
        categoryTabs.forEach(t => {
            t.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-md', 'shadow-blue-500/20');
            t.classList.add('border-gray-200', 'text-gray-600', 'bg-white');
        });

        this.classList.remove('border-gray-200', 'text-gray-600', 'bg-white');
        this.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-md', 'shadow-blue-500/20');

        filterProducts();
    });
});

// Initialize
filterProducts();

// Fungsi Delete
function deleteProduct(productId, productName) {
    if(typeof showConfirmModal === 'function') {
        showConfirmModal(
            'Hapus Produk',
            `Apakah Anda yakin ingin menghapus produk "${productName}"? Data tidak dapat dipulihkan.`,
            'fas fa-trash-alt text-red-600',
            'Hapus Sekarang',
            function() {
                executeDelete(productId);
            }
        );
    } else {
        if(confirm(`Hapus produk "${productName}"?`)) {
            executeDelete(productId);
        }
    }
}

function executeDelete(productId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/products/' + productId;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfInput);
    form.appendChild(methodInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

<style>
    /* Menghilangkan scrollbar pada filter kategori jika overflow di mobile */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection