@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="min-h-screen bg-[#fcfcfd] pb-12">
    <div class="bg-white border-b border-gray-100 shadow-sm/5">
        <div class="max-w-4xl mx-auto px-6 py-5 flex justify-between items-end">
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-bold text-indigo-500 uppercase tracking-[0.2em] mb-1">
                    <i class="fas fa-users-cog"></i>
                    <span>Manajemen Akses</span>
                </nav>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Tambah Pengguna Baru</h1>
            </div>
            
            <a href="{{ route('admin.users.index') }}" 
               class="text-xs font-bold text-gray-400 hover:text-indigo-600 transition-colors flex items-center gap-2 pb-1 group">
                <i class="fas fa-chevron-left text-[9px] transform group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 mt-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            @if($errors->any())
                <div class="p-4 bg-red-50 border-b border-red-100 text-red-600 text-xs font-medium">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-8">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Nama personil..."
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>

                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                placeholder="ID login unik..."
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="email@perusahaan.com"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>

                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Hak Akses (Role)</label>
                            <div class="relative">
                                <select name="role" required
                                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm appearance-none">
                                    <option value="">Pilih Role...</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                                    <option value="penerima" {{ old('role') === 'penerima' ? 'selected' : '' }}>Penerima</option>
                                    <option value="operator_cetak" {{ old('role') === 'operator_cetak' ? 'selected' : '' }}>Operator Cetak</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <div class="group max-w-md">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Password</label>
                        <input type="password" name="password" required
                            placeholder="Minimal 6 karakter..."
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-end gap-4 border-t border-gray-50 pt-8">
                    <a href="{{ route('admin.users.index') }}" 
                        class="text-sm font-bold text-gray-400 hover:text-gray-600 transition-all px-4">
                        Batalkan
                    </a>
                    <button type="submit" 
                        class="bg-gray-900 hover:bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-sm active:scale-95 text-sm flex items-center gap-2">
                        <i class="fas fa-user-plus text-[12px]"></i>
                        Daftarkan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection