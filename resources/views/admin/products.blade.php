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

    <div class="grid grid-cols-1 gap-4">
        <div id="productListContainer" class="space-y-4">
            @forelse($products as $product)
                <div class="product-row bg-white flex flex-col md:flex-row md:items-center justify-between p-5 border border-gray-100 rounded-2xl hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group relative overflow-hidden"
                    data-category="{{ in_array(strtolower($product->category), ['print', 'cetak']) ? 'cetak' : (in_array(strtolower($product->category), ['goods', 'barang']) ? 'barang' : strtolower($product->category)) }}">
                    
                    <div class="flex items-center gap-5 flex-1">
                        <div class="relative">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                                <i class="fas fa-box text-xl"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm"></div>
                        </div>
                        
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 text-lg group-hover:text-blue-600 transition-colors">{{ $product->name }}</h4>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded uppercase tracking-tight">SKU: {{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-gray-300">•</span>
                                
                                @php
                                    $categoryBadgeClass = 'text-gray-500';
                                    $categoryLabel = 'Other';
                                    
                                    if (in_array(strtolower($product->category), ['print', 'cetak'])) {
                                        $categoryBadgeClass = 'text-blue-600';
                                        $categoryLabel = 'Cetak';
                                    } elseif (strtolower($product->category) === 'studio') {
                                        $categoryBadgeClass = 'text-purple-600';
                                        $categoryLabel = 'Studio';
                                    } elseif (in_array(strtolower($product->category), ['goods', 'barang'])) {
                                        $categoryBadgeClass = 'text-orange-600';
                                        $categoryLabel = 'Barang';
                                    }
                                @endphp
                                <span class="text-xs font-bold {{ $categoryBadgeClass }} uppercase italic">{{ $categoryLabel }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row md:flex-col lg:flex-row items-center gap-8 my-4 md:my-0 px-0 md:px-8 border-t md:border-t-0 md:border-x border-gray-50 pt-4 md:pt-0">
                        <div class="text-left md:text-right lg:min-w-[150px]">
                            <p class="text-sm text-gray-400 font-medium">Harga Utama</p>
                            <p class="text-xl font-black text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="lg:min-w-[120px]">
                            <p class="text-sm text-gray-400 font-medium">Status Stok</p>
                            @if($product->unlimited_stock)
                                <span class="inline-flex items-center gap-1.5 text-green-600 font-bold text-sm">
                                    <i class="fas fa-infinity text-xs"></i> Unlimited
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 {{ $product->stock <= 5 ? 'text-red-500' : 'text-gray-700' }} font-bold text-sm">
                                    <i class="fas fa-warehouse text-xs"></i> {{ $product->stock ?? 0 }} Unit
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pl-0 md:pl-6">
                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-500 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                           title="Edit Produk">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" 
                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-500 hover:bg-red-600 hover:text-white transition-all shadow-sm" 
                           onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')"
                           title="Hapus Produk">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600 transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
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
    </div>

    @if($products->hasPages())
        <div class="mt-10 flex justify-center">
            <nav class="inline-flex items-center p-1 bg-white rounded-2xl shadow-sm border border-gray-100 gap-1">
                {{-- Previous --}}
                @if ($products->onFirstPage())
                    <span class="w-10 h-10 flex items-center justify-center text-gray-300">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                {{-- Pages --}}
                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if ($page == $products->currentPage())
                        <span class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white font-bold rounded-xl shadow-md shadow-blue-500/20">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="w-10 h-10 flex items-center justify-center text-gray-300">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </nav>
        </div>
    @endif
</div>

<script>
// Fungsi Filter & Search (Logika asli tetap dipertahankan)
const productSearchInput = document.getElementById('productSearch');
const categoryTabs = document.querySelectorAll('.category-tab');
let selectedCategory = 'all';

function filterProducts() {
    const searchTerm = productSearchInput?.value.toLowerCase() || '';

    document.querySelectorAll('.product-row').forEach(row => {
        const name = row.querySelector('h4')?.textContent.toLowerCase() || '';
        const sku = row.querySelector('.text-xs')?.textContent.toLowerCase() || '';
        const category = row.dataset.category.toLowerCase();

        const matchesSearch = name.includes(searchTerm) || sku.includes(searchTerm) || category.includes(searchTerm);
        const matchesCategory = selectedCategory === 'all' || category === selectedCategory;

        row.style.display = matchesSearch && matchesCategory ? 'flex' : 'none';
    });
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