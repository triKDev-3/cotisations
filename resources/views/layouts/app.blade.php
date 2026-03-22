<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cotisation') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .glass-dark { background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(20px); }

        /* Splash Screen Styles */
        #splash-screen {
            position: fixed; inset: 0; background: #0f111a; z-index: 9999;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: opacity 0.8s ease, visibility 0.8s;
        }
        #splash-screen.fade-out { opacity: 0; visibility: hidden; }
        .spinner-glow {
            width: 80px; height: 80px; border-radius: 50%;
            background: conic-gradient(from 0deg, transparent, #6366f1);
            mask: radial-gradient(farthest-side, transparent calc(100% - 4px), #000 0);
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <script>
        window.addEventListener('load', () => {
            const splash = document.getElementById('splash-screen');
            setTimeout(() => {
                splash.classList.add('fade-out');
            }, 600); // Petit délai pour l'effet "WOW"
        });
    </script>
</head>
<body class="bg-[#0f111a] text-gray-200 antialiased overflow-x-hidden">
    <!-- Splash Screen -->
    <div id="splash-screen">
        <div class="relative flex items-center justify-center">
            <div class="spinner-glow"></div>
            <div class="absolute w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center shadow-2xl shadow-indigo-500/40">
                <svg class="w-7 h-7 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </div>
        </div>
        <p class="mt-8 text-sm font-black uppercase tracking-[0.4em] text-gray-500 animate-pulse">Initialisation...</p>
    </div>
    
    <div class="flex h-screen overflow-hidden" x-data="{ mobileMenu: false }">
        
        <!-- Sidebar Backdrop (Mobile only) -->
        <div x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenu = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="mobileMenu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static inset-y-0 left-0 w-72 flex-shrink-0 bg-[#161925] border-r border-white/5 flex flex-col z-[60] transition-transform duration-300 ease-in-out">
            
            <!-- Sidebar Header -->
            <div class="p-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-white">{{ config('app.name') }}</span>
                </div>
                <!-- Close Button (Mobile) -->
                <button @click="mobileMenu = false" class="lg:hidden text-gray-500 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-grow px-4 space-y-2 mt-4 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all @if(request()->routeIs('dashboard')) bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 @else text-gray-500 hover:text-gray-300 hover:bg-white/5 @endif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="font-semibold">Tableau de Bord</span>
                </a>

                @if(Auth::user()->isCollecteur())
                    <a href="{{ route('collecteur.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all @if(request()->routeIs('collecteur.dashboard')) bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 @else text-gray-500 hover:text-gray-300 hover:bg-white/5 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-semibold">Espace Collecte</span>
                    </a>
                @endif
                
                <!-- Divider -->
                <div class="h-px bg-white/5 my-6 mx-4"></div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-gray-500 hover:text-gray-300 hover:bg-white/5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="font-semibold">Profil</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-red-500/10 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all duration-300 font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-grow flex flex-col min-w-0 bg-[#0f111a] relative">
            
            <!-- Top Header -->
            <header class="h-24 flex items-center justify-between px-6 lg:px-8 border-b border-white/5 bg-[#0f111a]/80 backdrop-blur-md sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <!-- Hamburger Menu (Mobile) -->
                    <button @click="mobileMenu = true" class="lg:hidden p-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    
                    <h2 class="text-xl lg:text-2xl font-bold text-white tracking-tight">
                        @isset($header) {{ $header }} @else {{ __('Dashboard') }} @endisset
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <!-- User Widget -->
                    <div class="flex items-center gap-3 bg-white/5 px-4 py-2 rounded-2xl border border-white/5">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-white leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[9px] text-gray-500 uppercase tracking-widest mt-1">{{ Auth::user()->role }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-full @if(request()->routeIs('collecteur.*')) bg-emerald-500/20 text-emerald-400 @else bg-indigo-500/20 text-indigo-400 @endif flex items-center justify-center font-black border border-white/5 text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content Scroll Area -->
            <div class="flex-grow overflow-y-auto p-6 lg:p-8 custom-scrollbar">
                {{ $slot }}
            </div>

            <!-- Dynamic Background Decorations (Changes based on section) -->
            @if(request()->routeIs('collecteur.*'))
                <div class="fixed top-0 right-0 w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>
                <div class="fixed bottom-0 left-0 w-[500px] h-[500px] bg-teal-600/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>
            @else
                <div class="fixed top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>
                <div class="fixed bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>
            @endif
        </main>

    </div>

</body>
</html>