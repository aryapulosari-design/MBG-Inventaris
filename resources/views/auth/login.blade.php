<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MBG Insights Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0F172A; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .hero-pattern {
            background-color: #0F172A;
            background-image: radial-gradient(circle at 15% 50%, rgba(37, 99, 235, 0.15) 0%, transparent 50%),
                              radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
        }
        .input-premium {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #F8FAFC;
            transition: all 0.3s ease;
        }
        .input-premium:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background: rgba(15, 23, 42, 0.8);
        }
        .btn-premium {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.39);
            transition: all 0.3s ease;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
        }
        .glow-icon {
            filter: drop-shadow(0 0 8px rgba(59,130,246,0.5));
        }
    </style>
</head>
<body class="hero-pattern min-h-screen text-slate-300 flex items-center justify-center p-4">

    <div class="w-full max-w-5xl flex flex-col md:flex-row glass-panel rounded-3xl overflow-hidden shadow-2xl relative z-10">
        
        <!-- Left Side: Branding / Presentation Focus -->
        <div class="w-full md:w-1/2 p-10 md:p-16 flex flex-col justify-center relative overflow-hidden bg-gradient-to-br from-slate-900 to-slate-800">
            <!-- Decorative circle -->
            <div class="absolute -top-24 -left-24 w-64 h-64 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full bg-emerald-600/10 blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600 mb-8 glow-icon">
                    <i class="bi bi-box-seam text-white text-3xl"></i>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight leading-tight">
                    Sistem Inventaris <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">
                        Makan Bergizi Gratis
                    </span>
                </h1>
                
                <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                    Platform logistik pintar untuk memantau, mengelola, dan mengamankan rantai pasok bahan makanan secara real-time.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="text-sm">Audit Log Permanen & Akuntabel</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="text-sm">Notifikasi Low-Stock Real-time</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-10 md:p-16 flex flex-col justify-center border-l border-white/5 relative">
            <div class="max-w-sm w-full mx-auto">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang Akses</h2>
                    <p class="text-sm text-slate-400">Silakan masuk menggunakan akun presentasi Anda.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm mb-4">
                            Terdapat kesalahan kredensial. Silakan coba lagi.
                        </div>
                    @endif

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-envelope text-slate-500"></i>
                            </div>
                            <input id="email" class="input-premium block w-full pl-10 pr-3 py-2.5 rounded-xl text-sm" type="email" name="email" value="{{ old('email', 'superadmin@mbg.id') }}" required autofocus autocomplete="username" placeholder="admin@mbg.id">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-slate-300">Kata Sandi</label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-lock text-slate-500"></i>
                            </div>
                            <input id="password" class="input-premium block w-full pl-10 pr-3 py-2.5 rounded-xl text-sm" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Remember Me & Submit -->
                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="rounded bg-slate-800 border-slate-600 text-blue-500 focus:ring-blue-500/50 w-4 h-4 transition-colors">
                            <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Ingat Saya</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-premium w-full py-3 rounded-xl text-white font-semibold text-sm mt-4 flex justify-center items-center gap-2">
                        <span>Masuk ke Dasbor</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-white/5 text-center">
                    <p class="text-xs text-slate-500 font-medium mb-3">AKUN DEMO PRESENTASI</p>
                    <div class="flex flex-col gap-2 text-xs">
                        <div class="flex justify-between items-center bg-slate-800/50 px-3 py-2 rounded-lg border border-white/5">
                            <span class="text-emerald-400 font-semibold"><i class="bi bi-person-fill-gear mr-1"></i> Admin</span>
                            <span class="font-mono text-slate-300">superadmin@mbg.id</span>
                        </div>
                        <div class="flex justify-between items-center bg-slate-800/50 px-3 py-2 rounded-lg border border-white/5">
                            <span class="text-slate-400 font-semibold"><i class="bi bi-eye mr-1"></i> Viewer</span>
                            <span class="font-mono text-slate-300">viewer@mbg.id</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
