@extends('layouts.app')

@section('title', 'Create Product - Admin')

@section('content')
<div class="min-h-screen bg-[#fcfcfd] pb-12">
    <div class="bg-white border-b border-gray-100 shadow-sm/5">
        <div class="max-w-4xl mx-auto px-6 py-5 flex justify-between items-end">
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-bold text-indigo-500 uppercase tracking-[0.2em] mb-1">
                    <i class="fas fa-cube"></i>
                    <span>Katalog Produk</span>
                </nav>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Tambah Produk Baru</h1>
            </div>
            
            <a href="{{ route('admin.products.index') }}" 
               class="text-xs font-bold text-gray-400 hover:text-indigo-600 transition-colors flex items-center gap-2 pb-1 group">
                <i class="fas fa-chevron-left text-[9px] transform group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 mt-10">
        <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.products.store') }}" method="POST" class="p-8 md:p-12">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-8">
                    
                    <div class="md:col-span-2 group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                            Nama Produk <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-box-open text-sm"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Contoh: Cetak Foto 10R Matte"
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 @error('name') border-red-400 @enderror">
                        </div>
                        @error('name') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                            Kategori <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 pointer-events-none">
                                <i class="fas fa-tags text-sm"></i>
                            </span>
                            <select name="category" required
                                class="w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 appearance-none @error('category') border-red-400 @enderror">
                                <option value="">Pilih Kategori</option>
                                <option value="cetak" {{ old('category') === 'cetak' ? 'selected' : '' }}>Cetak</option>
                                <option value="studio" {{ old('category') === 'studio' ? 'selected' : '' }}>Studio</option>
                                <option value="barang" {{ old('category') === 'barang' ? 'selected' : '' }}>Barang</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </span>
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">
                            Stok Awal
                        </label>
                        <div class="flex gap-3 items-center">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fas fa-cubes text-sm"></i>
                                </span>
                                <input type="number" name="stock" id="stockInput" value="{{ old('stock', 0) }}" min="0"
                                    class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 disabled:opacity-50">
                            </div>
                            <div class="flex items-center gap-2 bg-gray-50 px-4 py-3.5 rounded-2xl border border-gray-200">
                                <input type="checkbox" name="unlimited_stock" id="unlimitedStock" value="1" 
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded-md focus:ring-indigo-500"
                                    {{ old('unlimited_stock') ? 'checked' : '' }}>
                                <label for="unlimitedStock" class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Unlimited</label>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 mt-4">
                        <div class="flex items-center gap-4 mb-6">
                            <span class="h-px flex-1 bg-gray-100"></span>
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Pengaturan Harga (IDR)</span>
                            <span class="h-px flex-1 bg-gray-100"></span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-indigo-500 transition-colors">Harga Konsumen</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 font-bold text-sm">Rp</span>
                                    <input type="number" name="price" value="{{ old('price') }}" required placeholder="0"
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-lg font-bold text-gray-700">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-indigo-500 transition-colors">Harga Amatir</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 font-bold text-sm">Rp</span>
                                    <input type="number" name="amatir_price" value="{{ old('amatir_price') }}" required placeholder="0"
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-lg font-bold text-gray-700">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-50 flex flex-col md:flex-row gap-4">
                    <button type="submit" 
                        class="flex-[2] bg-gray-900 hover:bg-indigo-600 text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-gray-200 active:scale-[0.98] flex items-center justify-center gap-3 text-sm">
                        <i class="fas fa-check-circle"></i>
                        Simpan Produk Baru
                    </button>
                    <a href="{{ route('admin.products.index') }}" 
                        class="flex-1 px-8 py-4 rounded-2xl font-bold text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all flex items-center justify-center text-sm">
                        Batalkan
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-8 flex items-center gap-4 px-6 py-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                <i class="fas fa-lightbulb"></i>
            </div>
            <p class="text-[11px] text-indigo-800 leading-relaxed">
                <strong>Tips:</strong> Gunakan kategori <span class="font-bold text-indigo-900 uppercase">Cetak</span> atau <span class="font-bold text-indigo-900 uppercase">Studio</span> dan centang <span class="font-bold text-indigo-900 uppercase">Unlimited Stock</span> untuk produk berbentuk layanan jasa.
            </p>
        </div>
    </div>
</div>

<script>
    const stockInput = document.getElementById('stockInput');
    const unlimitedCheckbox = document.getElementById('unlimitedStock');

    const toggleStock = () => {
        if(unlimitedCheckbox.checked) {
            stockInput.disabled = true;
            stockInput.value = 0;
            stockInput.classList.add('bg-gray-100');
        } else {
            stockInput.disabled = false;
            stockInput.classList.remove('bg-gray-100');
        }
    };

    unlimitedCheckbox.addEventListener('change', toggleStock);
    window.addEventListener('load', toggleStock);
</script>

<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    
    form {
        animation: fadeInSlide 0.4s ease-out;
    }

    @keyframes fadeInSlide {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection