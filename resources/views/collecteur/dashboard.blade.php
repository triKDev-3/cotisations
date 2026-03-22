<x-app-layout>
    <x-slot name="header">
        {{ __('Espace Collecte') }}
    </x-slot>

    <div class="space-y-10">
        <!-- Welcome & Fast Stats -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight">Bonjour, <span class="text-indigo-400">Collecteur</span></h1>
                <p class="text-gray-500 mt-2 font-medium">Gérez les encaissements et les membres en toute simplicité.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="bg-[#161925] border border-white/5 p-4 rounded-2xl flex items-center gap-6 shadow-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest leading-none">Collecté</p>
                            <p class="text-lg font-bold text-white mt-1">{{ number_format($totalCollecteBrut ?? 0, 0, ',', ' ') }}</p>
                        </div>
                    </div>
                    <div class="w-px h-8 bg-white/5"></div>
                    <div class="flex items-center gap-3 text-emerald-400">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[9px] text-emerald-500/50 uppercase font-black tracking-widest leading-none">Solde Net</p>
                            <p class="text-lg font-black mt-1">{{ number_format($soldeGlobal ?? 0, 0, ',', ' ') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- New Payment Form (2/3) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold text-white mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white scale-90">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </span>
                            Nouvelle Transaction
                        </h3>

                        @if(session('success'))
                            <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl flex items-center gap-3 animate-in fade-in zoom-in duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-sm font-bold">{{ session('success') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('collecteur.cotisations.store') }}" class="space-y-8">
                            @csrf
                            
                            <!-- Type Selection Labels -->
                            <div class="flex items-center gap-4 bg-black/20 p-2 rounded-2xl border border-white/5 w-fit">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="versement" checked class="hidden peer">
                                    <span class="px-6 py-2 rounded-xl block text-xs font-bold uppercase tracking-widest transition-all peer-checked:bg-emerald-500 peer-checked:text-white text-gray-500 hover:text-gray-300">Versement</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="retrait" class="hidden peer">
                                    <span class="px-6 py-2 rounded-xl block text-xs font-bold uppercase tracking-widest transition-all peer-checked:bg-pink-600 peer-checked:text-white text-gray-500 hover:text-gray-300">Retrait</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-3 ml-2">Sélectionner le Membre</label>
                                    <select name="numero_compte" required class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none appearance-none">
                                        <option value="" class="bg-gray-900 italic">Rechercher un membre...</option>
                                        @foreach($jeunes as $jeune)
                                            <option value="{{ $jeune->numero_compte }}" class="bg-gray-900">{{ $jeune->name }} ({{ $jeune->numero_compte }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-3 ml-2">Montant (FCFA)</label>
                                    <input type="number" name="montant" placeholder="Ex: 5000" required class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-3 ml-2">Date d'effet</label>
                                    <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required class="w-full bg-black/20 border border-white/5 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                                </div>
                            </div>

                            <button type="submit" class="w-full md:w-auto px-10 py-5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold rounded-2xl shadow-xl shadow-emerald-500/20 transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-3">
                                Enregistrer la Transaction
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Activity Table with badges -->
                <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                        <h3 class="text-xl font-bold text-white tracking-tight">Derniers Mouvements</h3>
                    </div>
                    <div class="overflow-x-auto p-4">
                        <table class="w-full text-left">
                            <thead class="text-gray-500 text-[10px] font-black uppercase tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Type</th>
                                    <th class="px-6 py-4">Membre</th>
                                    <th class="px-6 py-4">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($historique as $mouvement)
                                    <tr class="hover:bg-white/[0.02] transition-colors">
                                        <td class="px-6 py-5 text-sm font-semibold text-gray-400">{{ $mouvement->date_paiement->format('d/m/Y') }}</td>
                                        <td class="px-6 py-5">
                                            @if($mouvement->type == 'versement')
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block mr-2 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                                <span class="text-[10px] font-bold text-emerald-500 uppercase">IN</span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-pink-500 inline-block mr-2 shadow-[0_0_8px_rgba(236,72,153,0.5)]"></span>
                                                <span class="text-[10px] font-bold text-pink-500 uppercase">OUT</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-white font-bold text-sm">{{ $mouvement->user->name ?? 'Inconnu' }}</span>
                                        </td>
                                        <td class="px-6 py-5 text-white font-black text-sm @if($mouvement->type == 'retrait') text-pink-400 @endif">
                                            {{ $mouvement->type == 'retrait' ? '-' : '+' }} {{ number_format($mouvement->montant, 0, ',', ' ') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-10 text-center text-gray-600 italic">Aucune donnée</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Members List (1/3) -->
            <div class="space-y-8">
                <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] flex flex-col h-[700px]">
                    <div class="p-8 border-b border-white/5 flex items-center justify-between">
                        <h3 class="text-xl font-black text-white tracking-tight">Membres</h3>
                        <a href="{{ route('collecteur.jeunes.create') }}" class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </a>
                    </div>
                    <div class="overflow-y-auto flex-grow p-4 space-y-3 custom-scrollbar">
                        @foreach($jeunes as $jeune)
                            <a href="{{ route('collecteur.jeunes.edit', $jeune) }}" class="flex items-center gap-4 p-4 rounded-3xl hover:bg-white/5 transition-all group">
                                <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-gray-500 font-bold group-hover:bg-indigo-500/10 group-hover:text-indigo-400">
                                    {{ substr($jeune->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors">{{ $jeune->name }}</p>
                                    <p class="text-[10px] text-gray-500 font-mono tracking-widest mt-1 uppercase">{{ $jeune->numero_compte }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>