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

    <div id="productListContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
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
            
            <div class="product-row bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group relative flex flex-col h-full"
                data-category="{{ $categoryKey }}"
                data-name="{{ strtolower($product->name) }}"
                data-sku="{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}">
                
                <!-- Image Placeholder Header -->
                <div class="bg-gradient-to-br {{ $categoryBg }} h-32 flex items-center justify-center relative overflow-hidden group">
                    <div class="absolute inset-0 opacity-10">
                        <i class="fas {{ $categoryIcon }} text-9xl text-white absolute -right-8 -top-8 transform rotate-12"></i>
                    </div>
                    <i class="fas {{ $categoryIcon }} text-6xl text-white/80 relative z-10 group-hover:scale-110 transition-transform"></i>
                    
                    <!-- Category Badge -->
                    <span class="absolute top-3 right-3 bg-white text-gray-900 text-xs font-black px-3 py-1.5 rounded-full shadow-lg">
                        {{ $categoryLabel }}
                    </span>
                    
                    <!-- Stock Indicator -->
                    <div class="absolute bottom-3 left-3 flex items-center gap-1 bg-white/95 px-2.5 py-1 rounded-full shadow-sm">
                        <div class="w-2 h-2 {{ $product->unlimited_stock ? 'bg-green-500' : ($product->stock > 0 ? 'bg-yellow-500' : 'bg-red-500') }} rounded-full"></div>
                        <span class="text-xs font-bold text-gray-700">
                            {{ $product->unlimited_stock ? '∞' : $product->stock ?? 0 }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4 flex-1 flex flex-col gap-3">
                    <!-- Product Name -->
                    <div>
                        <h4 class="font-black text-gray-900 text-base group-hover:text-blue-600 transition-colors line-clamp-2 product-name">
                            {{ $product->name }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-2 font-mono font-bold product-sku">SKU: {{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <!-- Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>

                    <!-- Price -->
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Harga Utama</p>
                        <p class="font-black text-lg text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 border-t border-gray-100 p-3 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                       class="flex items-center justify-center gap-2 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all font-bold text-xs shadow-sm active:scale-95">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>
                    <button type="button" 
                       class="flex items-center justify-center gap-2 py-2.5 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all font-bold text-xs shadow-sm active:scale-95" 
                       onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')"
                       title="Hapus Produk">
                        <i class="fas fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border-2 border-dashed border-gray-200 py-20 text-center">
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

    <div id="loadMoreContainer" class="mt-8 flex flex-col items-center gap-4">
        <p id="productCount" class="text-sm text-gray-600 font-medium"></p>
        <button id="loadMoreBtn" type="button" 
            class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-8 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="fas fa-arrow-down text-lg"></i> 
            <span>Tampilkan 10 Lebih Banyak</span>
        </button>
    </div>
</div>

<script>
// Expand/Load More Logic
const itemsPerLoad = 10;
let itemsShown = itemsPerLoad;
const productSearchInput = document.getElementById('productSearch');
const categoryTabs = document.querySelectorAll('.category-tab');
const loadMoreBtn = document.getElementById('loadMoreBtn');
const productCount = document.getElementById('productCount');
let selectedCategory = 'all';

function updateVisibility() {
    const allRows = document.querySelectorAll('.product-row');
    let visibleIndex = 0;
    let visibleCount = 0;
    let totalVisible = 0;

    // Hitung total yang sesuai filter
    allRows.forEach(row => {
        if (row.dataset.display !== 'hidden') {
            totalVisible++;
        }
    });

    // Update visibility berdasarkan itemsShown
    allRows.forEach(row => {
        if (row.dataset.display === 'hidden') {
            row.style.display = 'none';
        } else {
            visibleIndex++;
            if (visibleIndex <= itemsShown) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
    });

    // Update product count
    productCount.textContent = `Menampilkan ${visibleCount} dari ${totalVisible} produk`;

    // Show/Hide button
    if (visibleCount >= totalVisible) {
        loadMoreBtn.style.display = 'none';
    } else {
        loadMoreBtn.style.display = 'flex';
    }
}

function filterProducts() {
    const searchTerm = productSearchInput?.value.toLowerCase() || '';
    itemsShown = itemsPerLoad; // Reset ke 10 setiap kali search

    document.querySelectorAll('.product-row').forEach(row => {
        const name = row.dataset.name || '';
        const sku = row.dataset.sku || '';
        const category = row.dataset.category || '';

        const matchesSearch = !searchTerm || name.includes(searchTerm) || sku.toLowerCase().includes(searchTerm) || category.includes(searchTerm);
        const matchesCategory = selectedCategory === 'all' || category === selectedCategory;

        // Mark as hidden or not
        if (matchesSearch && matchesCategory) {
            row.dataset.display = 'visible';
        } else {
            row.dataset.display = 'hidden';
            row.style.display = 'none';
        }
    });

    updateVisibility();
}

productSearchInput?.addEventListener('input', filterProducts);

categoryTabs.forEach(tab => {
    tab.addEventListener('click', function() {
        selectedCategory = this.dataset.category;
        itemsShown = itemsPerLoad; // Reset ke 10 setiap kali filter

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

// Load More button
loadMoreBtn?.addEventListener('click', function() {
    itemsShown += itemsPerLoad;
    updateVisibility();
});

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