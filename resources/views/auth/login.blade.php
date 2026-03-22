<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Connexion') }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <!-- Animated background image/overlay -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/login_bg.png') }}" class="w-full h-full object-cover opacity-50 scale-105" alt="Background">
        <div class="absolute inset-0 bg-gradient-to-tr from-gray-900 via-gray-900/40 to-indigo-900/20"></div>
    </div>

    <!-- Main Card -->
    <div class="relative z-10 w-full max-w-md">
        
        <!-- Logo/Header section -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-2xl shadow-indigo-500/30 mb-6 group hover:rotate-6 transition-transform duration-300">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight">{{ config('app.name') }}</h1>
            <p class="text-gray-400 mt-2">Bienvenue. Connectez-vous à votre compte.</p>
        </div>

        <!-- Login Form with Glassmorphism -->
        <div class="backdrop-blur-xl bg-white/10 p-8 rounded-[2rem] border border-white/10 shadow-3xl shadow-black/50 overflow-hidden relative group">
            
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Login Input (Centric sur Numero de Compte) -->
                <div>
                    <label for="login" class="block text-sm font-medium text-indigo-300 mb-2 ml-1">Numéro de Compte</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus placeholder="Ex: ADMINROOT"
                            class="w-full bg-black/30 border border-white/10 rounded-2xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none text-white placeholder-white/20">
                    </div>
                    <x-input-error :messages="$errors->get('login')" class="mt-2" />
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-indigo-300 mb-2 ml-1">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" name="password" type="password" required placeholder="••••••••"
                            class="w-full bg-black/30 border border-white/10 rounded-2xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none text-white placeholder-white/20">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center group cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-black/30 border-white/10 text-indigo-500 focus:ring-indigo-500 transition-all">
                        <span class="ml-2 text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">Mot de passe oublié ?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-500/20 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] transform flex items-center justify-center gap-2">
                    Accéder au Dashboard
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </form>

            @if (Route::has('register'))
                <div class="mt-8 text-center text-sm">
                    <span class="text-gray-500">Pas encore de compte ?</span>
                    <a href="{{ route('register') }}" class="ml-1 text-indigo-400 hover:text-indigo-300 font-bold">Inscrivez-vous ici</a>
                </div>
            @endif

            <!-- Glow decoration -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <p class="text-center mt-10 text-gray-500 text-xs">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
        </p>
    </div>

</body>
</html>