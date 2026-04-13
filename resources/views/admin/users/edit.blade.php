@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="min-h-screen bg-[#fcfcfd] pb-12">
    <div class="bg-white border-b border-gray-100 shadow-sm/5">
        <div class="max-w-4xl mx-auto px-6 py-5 flex justify-between items-end">
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-bold text-indigo-500 uppercase tracking-[0.2em] mb-1">
                    <i class="fas fa-user-edit"></i>
                    <span>Modifikasi Profil</span>
                </nav>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Perbarui Pengguna</h1>
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
            
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="bg-indigo-50/50 rounded-xl px-4 py-3 flex items-center justify-between border border-indigo-100/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-bold text-gray-700">{{ $user->name }}</span>
                        </div>
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest bg-white px-3 py-1 rounded-full border border-indigo-50 shadow-sm">{{ $user->role }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>

                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>

                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Hak Akses</label>
                            <div class="relative">
                                <select name="role" required
                                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm appearance-none">
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                                    <option value="penerima" {{ old('role', $user->role) === 'penerima' ? 'selected' : '' }}>Penerima</option>
                                    <option value="operator_cetak" {{ old('role', $user->role) === 'operator_cetak' ? 'selected' : '' }}>Operator Cetak</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <div class="group max-w-md">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 group-focus-within:text-indigo-500 transition-colors">Ganti Password</label>
                        <p class="text-[10px] text-gray-400 mb-2 italic">Kosongkan jika tidak ingin mengubah password.</p>
                        <input type="password" name="password" 
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-end gap-4 border-t border-gray-50 pt-8">
                    <a href="{{ route('admin.users.index') }}" 
                        class="text-sm font-bold text-gray-400 hover:text-gray-600 transition-all px-4">
                        Batal
                    </a>
                    <button type="submit" 
                        class="bg-gray-900 hover:bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-sm active:scale-95 text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle text-[12px]"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection