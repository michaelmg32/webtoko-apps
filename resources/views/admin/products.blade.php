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

    <div id="productListContainer" class="space-y-3">
        @forelse($products as $product)
            @php
                $categoryKey = in_array(strtolower($product->category), ['print', 'cetak']) ? 'cetak' : (in_array(strtolower($product->category), ['goods', 'barang']) ? 'barang' : strtolower($product->category));
                $categoryBg = match($categoryKey) {
                    'cetak' => 'bg-blue-100 text-blue-700',
                    'barang' => 'bg-orange-100 text-orange-700',
                    'studio' => 'bg-purple-100 text-purple-700',
                    default => 'bg-gray-100 text-gray-600'
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
            
            <div class="product-row bg-white rounded-xl border border-gray-100 hover:shadow-lg hover:border-blue-200 transition-all duration-200 group flex items-center justify-between p-5"
                data-product-id="{{ $product->id }}"
                data-category="{{ $categoryKey }}"
                data-name="{{ strtolower($product->name) }}"
                data-sku="{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}">
                
                <!-- Left: Icon & Info -->
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 bg-gradient-to-br {{ match($categoryKey) { 'cetak' => 'from-blue-500 to-blue-600', 'barang' => 'from-orange-500 to-orange-600', 'studio' => 'from-purple-500 to-purple-600', default => 'from-gray-500 to-gray-600' } }} rounded-xl flex items-center justify-center text-white shadow-md">
                            <i class="fas {{ $categoryIcon }} text-lg"></i>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 text-base group-hover:text-blue-600 transition-colors truncate product-name">
                            {{ $product->name }}
                        </h4>
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="text-xs font-mono font-bold text-gray-500 product-sku">SKU: {{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-xs font-bold {{ $categoryBg }} px-2.5 py-1 rounded-full">
                                {{ $categoryLabel }}
                            </span>
                            @if($product->unlimited_stock)
                                <span class="text-xs font-bold text-green-600 flex items-center gap-1">
                                    <i class="fas fa-infinity"></i> Unlimited
                                </span>
                            @else
                                <span class="text-xs font-bold {{ $product->stock > 0 ? 'text-gray-700' : 'text-red-600' }}">
                                    {{ $product->stock ?? 0 }} Unit
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Middle: Price -->
                <div class="flex-shrink-0 mx-6 text-right">
                    <p class="text-xs text-gray-500 font-medium">Harga</p>
                    <p class="font-black text-lg text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>

                <!-- Right: Actions -->
                <div class="flex-shrink-0 flex gap-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                       class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                       title="Edit Produk">
                        <i class="fas fa-edit text-sm"></i>
                    </a>
                    <button type="button" 
                       class="w-10 h-10 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" 
                       onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')"
                       title="Hapus Produk">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
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

    <!-- Infinite Scroll Trigger -->
    <div id="scrollTrigger" class="mt-12 flex justify-center">
        <div class="text-center py-8">
            <div class="inline-flex items-center gap-2">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></div>
                <p class="text-sm text-gray-500">Scroll untuk muat lebih banyak...</p>
            </div>
        </div>
    </div>

    <!-- Product Count -->
    <div class="mt-2 text-center">
        <p id="productCount" class="text-sm font-medium text-gray-600"></p>
    </div>
</div>

<script>
// Infinite Scroll Logic with Full Search
const itemsPerLoad = 10;
let itemsShown = itemsPerLoad;
const productSearchInput = document.getElementById('productSearch');
const categoryTabs = document.querySelectorAll('.category-tab');
const productCount = document.getElementById('productCount');
const scrollTrigger = document.getElementById('scrollTrigger');
let selectedCategory = 'all';
let isLoading = false;

// Store all products data
const allProductsData = @json($products->map(fn($p) => [
    'id' => $p->id,
    'name' => strtolower($p->name),
    'sku' => str_pad($p->id, 4, '0', STR_PAD_LEFT),
    'category' => in_array(strtolower($p->category), ['print', 'cetak']) ? 'cetak' : (in_array(strtolower($p->category), ['goods', 'barang']) ? 'barang' : strtolower($p->category))
]));

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

    // Sembunyikan scroll trigger jika sudah semua
    if (visibleCount >= totalVisible || totalVisible === 0) {
        scrollTrigger.style.display = 'none';
    } else {
        scrollTrigger.style.display = 'block';
    }
}

function filterProducts() {
    const searchTerm = productSearchInput?.value.toLowerCase() || '';
    itemsShown = itemsPerLoad; // Reset ke 10 setiap kali search

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

// Setup Intersection Observer untuk infinite scroll
const observerOptions = {
    root: null,
    rootMargin: '100px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !isLoading) {
            loadMoreItems();
        }
    });
}, observerOptions);

// Observe scroll trigger element
if (scrollTrigger) {
    observer.observe(scrollTrigger);
}

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