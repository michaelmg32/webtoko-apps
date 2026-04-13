<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Studio Presisi')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-active { transform: translateX(0) !important; }
        .sidebar-item-active {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white !important;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
        }
        /* Efek transisi halus */
        .transition-soft { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Sidebar Collapsed State */
        .sidebar-collapsed {
            width: 5.5rem !important;
        }
        
        .sidebar-collapsed .sidebar-text {
            display: none;
        }
        
        .sidebar-collapsed .sidebar-logo-text {
            display: none;
        }
        
        .sidebar-collapsed nav {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        .sidebar-collapsed a, .sidebar-collapsed .sidebar-section-label {
            justify-content: center;
            padding: 0.75rem !important;
        }
        
        .sidebar-collapsed a i, .sidebar-collapsed .sidebar-section-label i {
            margin-right: 0 !important;
        }
        
        .sidebar-collapsed .user-info {
            justify-content: center;
        }
        
        .sidebar-collapsed .user-name, .sidebar-collapsed .user-role {
            display: none;
        }
        
        /* Smooth transition for width change */
        aside {
            width: 18rem;
        }

        /* Elegant Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(148, 163, 184, 0.4) 0%, rgba(100, 116, 139, 0.4) 100%);
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(148, 163, 184, 0.6) 0%, rgba(100, 116, 139, 0.6) 100%);
            background-clip: padding-box;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:active {
            background: linear-gradient(180deg, rgba(71, 85, 105, 0.8) 0%, rgba(51, 65, 85, 0.8) 100%);
            background-clip: padding-box;
        }
        
        @media (max-width: 1024px) {
            .sidebar-mobile { transform: translateX(-100%); }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-700">
    <div class="flex h-screen overflow-hidden">
        
        <aside id="sidebar" class="sidebar-mobile fixed inset-y-0 left-0 z-50 w-72 bg-[#0F172A] flex flex-col transition-soft lg:relative lg:translate-x-0 shadow-2xl">
            <div class="p-6 border-b border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-camera text-white text-lg"></i>
                        </div>
                        <div class="sidebar-logo-text">
                            <h1 class="font-bold text-white tracking-tight text-xl leading-none">Bukit Foto</h1>
                            <span class="text-[10px] uppercase tracking-[0.2em] text-green-400 font-bold">Studio</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="toggleSidebarCollapse()" class="hidden lg:block p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-soft">
                            <i class="fas fa-chevron-left text-lg"></i>
                        </button>
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-soft">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar transition-soft">
                <p class="sidebar-section-label flex items-center gap-3 px-4 py-2 text-[11px] font-bold text-slate-500 mb-4 uppercase tracking-[0.15em]">
                    <i class="fas fa-grip-vertical text-sm flex-shrink-0"></i>
                    <span class="sidebar-text">Menu Utama</span>
                </p>
                
                @if(auth()->user()->role === 'penerima' || auth()->user()->role === 'admin')
                    <a href="{{ route('penerima.orders.create') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-soft group {{ request()->is('*/orders/create') ? 'sidebar-item-active' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-plus-circle text-lg flex-shrink-0"></i>
                        <span class="sidebar-text text-sm font-medium">Buat Pesanan</span>
                    </a>
                @endif

                <a href="{{ route('orderstatus.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-soft group {{ request()->is('orderstatus*') ? 'sidebar-item-active' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-tasks text-lg flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Status Pesanan</span>
                </a>

                @if(auth()->user()->role === 'kasir' || auth()->user()->role === 'admin')
                    <a href="{{ route('kasir.orders.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-soft group {{ request()->is('kasir/orders*') ? 'sidebar-item-active' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-wallet text-lg flex-shrink-0"></i>
                        <span class="sidebar-text text-sm font-medium">Pembayaran</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'operator_cetak' || auth()->user()->role === 'admin')
                    <a href="{{ route('operator.orders.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-soft group {{ request()->is('operator*') ? 'sidebar-item-active' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fas fa-print text-lg flex-shrink-0"></i>
                        <span class="sidebar-text text-sm font-medium">Cetak Pesanan</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <div class="mt-8 pt-6 border-t border-slate-800">
                        <p class="sidebar-section-label flex items-center gap-3 px-4 py-2 text-[11px] font-bold text-slate-500 mb-4 uppercase tracking-[0.15em]">
                            <i class="fas fa-sliders-h text-sm flex-shrink-0"></i>
                            <span class="sidebar-text">Admin Panel</span>
                        </p>
                        
                        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ request()->is('admin/products*') ? 'sidebar-item-active' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-box-open text-lg flex-shrink-0"></i>
                            <span class="sidebar-text text-sm font-medium">Data Produk</span>
                        </a>

                        <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ request()->is('admin/reports*') ? 'sidebar-item-active' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-chart-line text-lg flex-shrink-0"></i>
                            <span class="sidebar-text text-sm font-medium">Laporan</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ request()->is('admin/users*') ? 'sidebar-item-active' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fas fa-users-cog text-lg flex-shrink-0"></i>
                            <span class="sidebar-text text-sm font-medium">Kelola Staff</span>
                        </a>
                    </div>
                @endif
            </nav>

            <div class="p-4 bg-[#1E293B] border-t border-slate-800">
                <div class="user-info flex items-center gap-3 p-2 rounded-lg transition-soft">
                    <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center border border-green-500/20 text-green-500 flex-shrink-0">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="flex-1 min-w-0 sidebar-text">
                        <p class="user-name text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="user-role text-[10px] text-green-400 font-bold uppercase tracking-wider">{{ auth()->user()->role }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-red-400 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden backdrop-blur-sm"></div>

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h2 class="text-lg font-bold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-slate-100 border border-slate-200 px-4 py-2 rounded-2xl">
                    <div class="flex flex-col items-end hidden md:block">
                        <span class="text-[9px] font-bold text-slate-400 uppercase leading-none">Waktu Lokal</span>
                    </div>
                    <div class="w-[2px] h-6 bg-slate-300 hidden md:block"></div>
                    <span id="digitalClock" class="text-sm font-black text-slate-700 tabular-nums tracking-wider">00:00:00</span>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @if($errors->any() || session('success'))
                        <div class="mb-6 space-y-2">
                            @if($errors->any())
                                <div class="flex items-center gap-3 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
                                    <i class="fas fa-circle-exclamation"></i>
                                    <span>Input tidak valid. Periksa kembali form Anda.</span>
                                </div>
                            @endif
                            @if(session('success'))
                                <div class="flex items-center gap-3 bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-xl text-sm font-bold shadow-sm">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    {{ session('success') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="toastContainer" class="fixed bottom-6 right-6 left-6 sm:left-auto space-y-3 z-[100] pointer-events-none"></div>

    <div id="confirmModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-sm w-full overflow-hidden border border-slate-100 transition-all transform scale-95">
            <div class="p-8 text-center">
                <div id="confirmIcon" class="w-20 h-20 mx-auto rounded-3xl bg-slate-50 flex items-center justify-center text-3xl mb-6 text-slate-800 shadow-inner"></div>
                <h2 id="confirmTitle" class="text-xl font-black text-slate-900 mb-2">Konfirmasi</h2>
                <p id="confirmMessage" class="text-sm text-slate-500 leading-relaxed mb-8 font-medium"></p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="closeConfirmModal()" class="flex-1 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold text-sm transition-soft order-2 sm:order-1">
                        Batal
                    </button>
                    <button type="button" id="confirmBtn" class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-bold text-sm transition-soft shadow-lg shadow-green-200 order-1 sm:order-2">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Sidebar Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('sidebar-active');
            overlay.classList.toggle('hidden');
        }

        // Toggle Sidebar Collapse (Desktop)
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = sidebar.querySelector('button[onclick="toggleSidebarCollapse()"] i');
            sidebar.classList.toggle('sidebar-collapsed');
            
            if (sidebar.classList.contains('sidebar-collapsed')) {
                toggleBtn.classList.remove('fa-chevron-left');
                toggleBtn.classList.add('fa-chevron-right');
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                toggleBtn.classList.remove('fa-chevron-right');
                toggleBtn.classList.add('fa-chevron-left');
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        }

        // Restore sidebar state on page load
        window.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.add('sidebar-collapsed');
                const toggleBtn = sidebar.querySelector('button[onclick="toggleSidebarCollapse()"] i');
                toggleBtn.classList.remove('fa-chevron-left');
                toggleBtn.classList.add('fa-chevron-right');
            }
        });

        // Jam Real-time
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const clockElement = document.getElementById('digitalClock');
            if(clockElement) clockElement.textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Notifikasi Toast yang Responsif
        function showNotification(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const bgStyles = {
                success: 'border-l-green-500 text-green-800',
                error: 'border-l-red-500 text-red-800',
                info: 'border-l-blue-500 text-blue-800'
            };
            
            const toast = document.createElement('div');
            toast.className = `w-full sm:w-[350px] bg-white p-5 rounded-2xl shadow-2xl border-l-4 flex items-center gap-4 transition-soft transform translate-y-20 opacity-0 pointer-events-auto ${bgStyles[type] || bgStyles.info}`;
            toast.innerHTML = `<i class="fas fa-info-circle text-xl"></i><span class="text-sm font-bold leading-tight">${message}</span>`;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-20', 'opacity-0');
            }, 100);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-10');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        // Modal Logic
        function showConfirmModal(title, message, icon, confirmText, callback) {
            const modal = document.getElementById('confirmModal');
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmIcon').innerHTML = `<i class="${icon}"></i>`;
            document.getElementById('confirmBtn').textContent = confirmText;
            modal.classList.remove('hidden');
            setTimeout(() => modal.firstElementChild.classList.remove('scale-95'), 10);
            window.confirmCallback = callback;
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        document.getElementById('confirmBtn').addEventListener('click', () => {
            if (window.confirmCallback) window.confirmCallback();
            closeConfirmModal();
        });
    </script>
</body>
</html>