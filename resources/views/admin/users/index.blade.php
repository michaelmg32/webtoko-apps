@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="p-6 md:p-10 bg-gray-50 min-h-screen">
    
    <div class="max-w-5xl mx-auto">
        
        <nav class="flex text-sm text-gray-500 mb-4">
            <ol class="list-none p-0 inline-flex items-center space-x-2">
                <li><a href="#" class="hover:text-blue-600 transition">Admin</a></li>
                <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                <li class="text-gray-800 font-medium">Manajemen Pengguna</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-2 border-b border-gray-200 pb-3">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-xs text-gray-500 mt-0.5">Kelola dan atur hak akses akun pengguna sistem Anda.</p>
            </div>
            
            <div class="flex items-center gap-4 mt-3 md:mt-0">
                <div class="hidden sm:block text-right pr-4 border-r border-gray-200">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Akun</p>
                <p class="text-xl font-black text-blue-600 leading-none">{{ $users->count() }}</p>
                </div>
                
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-100 transition-all active:scale-95 gap-2 group">
                    <i class="fas fa-plus text-xs group-hover:rotate-90 transition-transform"></i>
                    Tambah Pengguna
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-2 p-4 bg-white border-l-4 border-red-500 shadow-sm rounded-r-xl flex gap-3 items-start animate-fadeIn">
                <div class="mt-1 bg-red-100 p-2 rounded-full text-red-600 flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-sm"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-800 text-sm">Terjadi Kesalahan!</h4>
                    <ul class="text-xs text-red-600 mt-1 list-disc ml-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-2 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl flex justify-between items-center animate-fadeIn shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-500 text-white w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.style.display='none'" class="text-emerald-400 hover:text-emerald-600 transition p-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="mb-2">
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Cari pengguna..." 
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition shadow-sm">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="usersTable">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-100">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest w-2/5">Informasi Pengguna</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest w-1/4">Kontak</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Akses / Role</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-blue-50/30 transition-colors group user-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-100 to-blue-50 flex items-center justify-center text-blue-600 font-bold border-2 border-white shadow-sm group-hover:scale-110 transition-transform flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-900 truncate user-name">{{ $user->name }}</p>
                                            <p class="text-[11px] text-gray-400 font-mono mt-0.5 truncate">{{ '@' . $user->username }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600 truncate user-email">
                                        <i class="far fa-envelope text-gray-300"></i>
                                        {{ $user->email }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $roleStyles = [
                                            'admin' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            'kasir' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'penerima' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'default' => 'bg-slate-50 text-slate-600 border-slate-100'
                                        ];
                                        $style = $roleStyles[$user->role] ?? $roleStyles['default'];
                                    @endphp
                                    <span class="inline-block px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border {{ $style }}">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="p-2.5 bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm"
                                           title="Edit Data">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline deleteUserForm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="p-2.5 bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-sm delete-btn" 
                                                    data-user-name="{{ $user->name }}"
                                                    title="Hapus Pengguna">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full mb-4">
                                        <i class="fas fa-users-slash text-3xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800">Tidak Ada Data</h3>
                                    <p class="text-gray-500 text-sm mt-1">Belum ada pengguna yang terdaftar di sistem.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div> </div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Script Konfirmasi Hapus
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userName = this.dataset.userName;
                const form = this.closest('.deleteUserForm');
                
                // Gunakan fungsi modal jika ada, jika tidak fallback ke confirm bawaan browser
                if (typeof showConfirmModal === 'function') {
                    showConfirmModal(
                        'Hapus Pengguna',
                        `Apakah Anda yakin ingin menghapus "${userName}"? Data ini tidak dapat dikembalikan.`,
                        'fas fa-user-times text-red-600',
                        'Ya, Hapus',
                        function() {
                            form.submit();
                        }
                    );
                } else {
                    if(confirm(`Apakah Anda yakin ingin menghapus pengguna "${userName}"?\nTindakan ini tidak dapat dibatalkan.`)) {
                        form.submit();
                    }
                }
            });
        });

        // 2. Script Pencarian Sederhana
        const searchInput = document.getElementById('searchInput');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('.user-row');
                
                rows.forEach(row => {
                    const name = row.querySelector('.user-name').textContent.toLowerCase();
                    const email = row.querySelector('.user-email').textContent.toLowerCase();
                    
                    if(name.includes(term) || email.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection