<x-app-layout>
    <x-slot name="header">
        <span class="text-emerald-500">Profil Membre</span>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        <!-- Header Info -->
        <div class="bg-gradient-to-r from-emerald-600/20 to-teal-600/20 border border-emerald-500/20 rounded-[2rem] p-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[1.5rem] bg-white/5 border border-white/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight italic">{{ $user->name }}</h1>
                    <p class="text-emerald-500/60 text-xs font-bold uppercase tracking-widest mt-1">Modification des paramètres de compte</p>
                </div>
            </div>
            <a href="{{ route('collecteur.dashboard') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all text-xs font-bold uppercase tracking-widest border border-white/5">
                Retour
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-600/5 blur-[80px] -z-10 rounded-full"></div>
            
            <form method="POST" action="{{ route('collecteur.jeunes.update', $user) }}" class="space-y-10">
                @csrf
                @method('PUT')

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
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                            class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white text-lg font-bold focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                    </div>

                    <!-- Numero Compte (READ ONLY) -->
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Identifiant (Immuable)</label>
                        <div class="w-full bg-white/5 border border-white/5 rounded-2xl py-4 px-6 flex items-center justify-between opacity-80">
                            <span class="text-lg font-black text-emerald-500">{{ $user->numero_compte }}</span>
                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        </div>
                        <input type="hidden" name="numero_compte" value="{{ $user->numero_compte }}">
                    </div>

                    <!-- Password Security Section -->
                    <div class="md:col-span-2 pt-6 border-t border-white/5">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Sécurité & Mot de passe
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Nouveau Mot de passe (Optionnel)</label>
                                <input type="password" name="password" placeholder="••••••••"
                                    class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-3 ml-2">Confirmer Nouveau Mot de passe</label>
                                <input type="password" name="password_confirmation" placeholder="••••••••"
                                    class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white font-medium focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
                    <button type="submit" class="w-full md:w-auto px-12 py-5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-500/20 transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-4">
                        Soumettre les modifications
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </button>

                    <button type="button" 
                        onclick="if(confirm('Êtes-vous sûr de vouloir supprimer {{ $user->name }} ? Toutes ses cotisations seront perdues.')) document.getElementById('delete-form').submit();"
                        class="text-rose-500 hover:text-rose-400 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Supprimer le Compte
                    </button>
                </div>
            </form>

            <form id="delete-form" action="{{ route('collecteur.jeunes.destroy', $user) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>