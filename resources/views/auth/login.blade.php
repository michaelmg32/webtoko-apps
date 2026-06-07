<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bukit Foto Studio</title>
    <meta name="description" content="Sistem Manajemen Internal Bukit Foto Studio. Silakan masuk dengan akun staf Anda untuk mengelola pesanan, kasir, dan antrean cetak.">
    <meta name="robots" content="index, follow">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="any" href="{{ asset('bukitfoto.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('bukitfoto.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        slate: {
                            850: '#151e32',
                            900: '#0F172A',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
        }
        .input-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes float {
            0% { transform: translateY(0px) rotate(3deg); }
            50% { transform: translateY(-10px) rotate(0deg); }
            100% { transform: translateY(0px) rotate(3deg); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center relative overflow-hidden text-slate-800 selection:bg-green-500 selection:text-white">
    <!-- Ambient Background Glows -->
    <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-green-500/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[60%] h-[60%] bg-emerald-600/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-[20%] right-[10%] w-[30%] h-[30%] bg-blue-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-[440px] p-6 relative z-10">
        <!-- Logo & Brand Header -->
        <div class="text-center mb-10 animate-[fade-in-down_0.8s_ease-out]">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-green-400 to-green-600 rounded-[2rem] shadow-2xl shadow-green-500/40 mb-6 animate-float relative">
                <div class="absolute inset-0 bg-white/20 rounded-[2rem] border border-white/40"></div>
                <i class="fas fa-camera text-white text-4xl relative z-10"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-2">Bukit Foto</h1>
            <div class="flex items-center justify-center gap-3">
                <div class="h-px w-8 bg-green-500/50"></div>
                <p class="text-xs font-bold text-green-400 uppercase tracking-[0.25em]">Studio Management</p>
                <div class="h-px w-8 bg-green-500/50"></div>
            </div>
        </div>

        <!-- Glass Login Card -->
        <div class="glass-panel rounded-[2.5rem] p-8 sm:p-10 shadow-2xl shadow-black/50 border border-white/10 animate-[fade-in-up_0.8s_ease-out] relative overflow-hidden">
            <!-- Decorative shine -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-green-400 to-transparent opacity-50"></div>

            <!-- Alerts -->
            @if ($errors->any())
                <div class="mb-8 bg-red-50 border border-red-100 rounded-2xl p-4 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-exclamation text-red-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm font-semibold text-red-800">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-8 bg-green-50 border border-green-100 rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('authenticate') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Username Input -->
                <div class="space-y-2">
                    <label for="username" class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Username</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-300 group-focus-within:text-green-500 text-slate-400">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" 
                               class="w-full bg-slate-50/50 border-2 border-slate-100 text-slate-800 rounded-2xl pl-11 pr-4 py-4 focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 input-transition font-semibold outline-none placeholder:text-slate-400 placeholder:font-medium" 
                               placeholder="Masukkan username" required autocomplete="username">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-300 group-focus-within:text-green-500 text-slate-400">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" 
                               class="w-full bg-slate-50/50 border-2 border-slate-100 text-slate-800 rounded-2xl pl-11 pr-4 py-4 focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 input-transition font-semibold outline-none placeholder:text-slate-400 placeholder:font-medium" 
                               placeholder="Masukkan password" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-green-500/30 input-transition hover:-translate-y-1 flex items-center justify-center gap-3 group mt-8 relative overflow-hidden">
                    <span class="relative z-10 text-sm tracking-wide">MASUK KE SISTEM</span>
                    <i class="fas fa-arrow-right text-sm group-hover:translate-x-1.5 input-transition relative z-10"></i>
                    <!-- Shine effect -->
                    <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12"></div>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-10 text-center">
            <p class="text-slate-500 text-xs font-medium">
                &copy; {{ date('Y') }} <span class="font-bold text-slate-300">Bukit Foto Studio</span>
            </p>
            <p class="mt-1.5 text-[10px] text-slate-600 uppercase tracking-widest">Sistem Manajemen Internal</p>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
    </style>
</body>
</html>
