<x-app-layout>
    <x-slot name="header">
        <span class="text-emerald-500">Nouveau Membre</span>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        <!-- Header Info -->
        <div class="bg-gradient-to-r from-emerald-600/20 to-teal-600/20 border border-emerald-500/20 rounded-[2rem] p-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-500 flex items-center justify-center text-white shadow-xl shadow-emerald-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight italic">Inscription Membre</h1>
                    <p class="text-emerald-500/60 text-xs font-bold uppercase tracking-widest mt-1">Espace de gestion administrative</p>
                </div>
            </div>
            <a href="{{ route('collecteur.dashboard') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all text-xs font-bold uppercase tracking-widest border border-white/5">
                Retour
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-600/5 blur-[80px] -z-10 rounded-full"></div>
            
            <form method="POST" action="{{ route('collecteur.jeunes.store') }}" class="space-y-10">
                @csrf

                @if($errors->any())
                    <div class="p-6 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-sm font-medium">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Nom & Prénoms</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Ex: Jean Dupont" 
                            class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white text-lg font-bold focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="jean@exemple.com"
                            class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                    </div>

                    <!-- Account ID Info -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Identifiant de Compte</label>
                        <div class="w-full bg-emerald-500/5 border border-emerald-500/20 rounded-2xl py-4 px-6 flex items-center gap-3">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-bold text-emerald-500/80 uppercase italic tracking-wider">Génération Automatique active</span>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Définir Mot de passe</label>
                        <input type="password" name="password" required
                            class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Confirmer Mot de passe</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                    </div>
                </div>

                <div class="pt-6 border-t border-white/5">
                    <button type="submit" class="w-full md:w-auto px-12 py-5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-500/20 transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-4">
                        Créer le Membre
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                    <p class="mt-6 text-center md:text-left text-[10px] text-gray-600 font-bold uppercase tracking-widest">
                        L'utilisateur recevra ses accès et pourra se connecter avec son numéro de compte généré.
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>