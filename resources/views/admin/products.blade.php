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
            <div class="product-row bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group relative flex flex-col h-full"
                data-category="{{ in_array(strtolower($product->category), ['print', 'cetak']) ? 'cetak' : (in_array(strtolower($product->category), ['goods', 'barang']) ? 'barang' : strtolower($product->category)) }}">
                
                <!-- Header dengan Icon -->
                <div class="bg-gradient-to-br from-blue-50 to-gray-50 p-4 flex items-center justify-between border-b border-gray-100">
                    <div class="relative">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform shadow-sm">
                            <i class="fas fa-box text-lg"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full shadow-sm"></div>
                    </div>
                    
                    @php
                        $categoryBadgeClass = 'bg-gray-100 text-gray-600';
                        $categoryLabel = 'Other';
                        
                        if (in_array(strtolower($product->category), ['print', 'cetak'])) {
                            $categoryBadgeClass = 'bg-blue-100 text-blue-700';
                            $categoryLabel = 'Cetak';
                        } elseif (strtolower($product->category) === 'studio') {
                            $categoryBadgeClass = 'bg-purple-100 text-purple-700';
                            $categoryLabel = 'Studio';
                        } elseif (in_array(strtolower($product->category), ['goods', 'barang'])) {
                            $categoryBadgeClass = 'bg-orange-100 text-orange-700';
                            $categoryLabel = 'Barang';
                        }
                    @endphp
                    <span class="text-xs font-bold {{ $categoryBadgeClass }} px-2.5 py-1 rounded-lg">{{ $categoryLabel }}</span>
                </div>

                <!-- Content -->
                <div class="p-4 flex-1 flex flex-col gap-3">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm group-hover:text-blue-600 transition-colors line-clamp-2">
                            {{ $product->name }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-1 font-medium">SKU: {{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <!-- Price -->
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <p class="text-xs text-gray-600 font-medium mb-0.5">Harga Utama</p>
                        <p class="font-black text-blue-600 text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>

                    <!-- Stock Status -->
                    <div class="flex items-center gap-2">
                        <i class="fas fa-warehouse text-xs text-gray-400"></i>
                        <div class="text-xs">
                            <p class="text-gray-600 font-medium">Status Stok</p>
                            @if($product->unlimited_stock)
                                <span class="inline-flex items-center gap-1 text-green-600 font-bold">
                                    <i class="fas fa-infinity text-xs"></i> Unlimited
                                </span>
                            @else
                                <span class="text-gray-700 font-bold">{{ $product->stock ?? 0 }} Unit</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 border-t border-gray-100 p-3 flex gap-2 justify-end">
                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                       class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all font-medium text-sm shadow-sm active:scale-95"
                       title="Edit Produk">
                        <i class="fas fa-edit text-xs"></i>
                        <span class="hidden sm:inline">Edit</span>
                    </a>
                    <button type="button" 
                       class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all font-medium text-sm shadow-sm active:scale-95" 
                       onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')"
                       title="Hapus Produk">
                        <i class="fas fa-trash text-xs"></i>
                        <span class="hidden sm:inline">Hapus</span>
                    </button>
                </div>

                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600 transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
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

    allRows.forEach((row, index) => {
        const isHidden = row.style.display === 'none';
        
        if (!isHidden) {
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
    const totalVisible = Array.from(allRows).filter(row => row.dataset.display !== 'hidden').length;
    productCount.textContent = `Menampilkan ${Math.min(visibleCount, totalVisible)} dari ${totalVisible} produk`;

    // Hide button jika sudah menampilkan semua
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
        const name = row.querySelector('h4')?.textContent.toLowerCase() || '';
        const sku = row.querySelector('.text-xs')?.textContent.toLowerCase() || '';
        const category = row.dataset.category.toLowerCase();

        const matchesSearch = name.includes(searchTerm) || sku.includes(searchTerm) || category.includes(searchTerm);
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