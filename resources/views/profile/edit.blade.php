@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="min-h-screen bg-[#fcfcfd] pb-12">
    <div class="bg-white border-b border-gray-100 shadow-sm/5">
        <div class="max-w-4xl mx-auto px-6 py-5 flex justify-between items-end">
            <div>
                <nav class="flex items-center gap-2 text-[10px] font-bold text-indigo-500 uppercase tracking-[0.2em] mb-1">
                    <i class="fas fa-user-circle"></i>
                    <span>Akun Saya</span>
                </nav>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight">Edit Profil</h1>
            </div>
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

            <form action="{{ route('profile.update') }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Username (Tidak dapat diubah)</label>
                            <input type="text" value="{{ $user->username }}" readonly disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 text-sm cursor-not-allowed">
                        </div>

                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Role</label>
                            <input type="text" value="{{ strtoupper($user->role) }}" readonly disabled
                                class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 text-sm cursor-not-allowed font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>

                        <div class="group">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                        </div>
                    </div>

                    <div class="group max-w-md">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-focus-within:text-indigo-500 transition-colors">Password Baru (Opsional)</label>
                        <input type="password" name="password"
                            placeholder="Biarkan kosong jika tidak ingin mengubah password"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white transition-all outline-none text-gray-700 text-sm">
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-end gap-4 border-t border-gray-50 pt-8">
                    <button type="submit" 
                        class="bg-gray-900 hover:bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-sm active:scale-95 text-sm flex items-center gap-2">
                        <i class="fas fa-save text-[12px]"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
