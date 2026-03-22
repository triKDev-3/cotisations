<x-app-layout>
    <x-slot name="header">
        {{ __('Tableau de Bord') }}
    </x-slot>

    <div x-data="{ 
    notification: null,
    init() {
        const userId = {{ Auth::id() }};
        const f = window.firebase;
        const q = f.query(
            f.collection(window.db, 'cotisations'),
            f.where('user_id', '==', userId),
            f.orderBy('date_enregistrement', 'desc'),
            f.limit(1)
        );

        f.onSnapshot(q, (snapshot) => {
            snapshot.docChanges().forEach((change) => {
                if (change.type === 'added') {
                    const data = change.doc.data();
                    const now = Date.now();
                    if (data.date_enregistrement && (now - data.date_enregistrement.toMillis() < 10000)) {
                        this.notification = `Nouvelle cotisation de ${data.montant.toLocaleString()} FCFA !`;
                        setTimeout(() => { this.notification = null }, 7000);
                    }
                }
            });
        });
    }
}">
    <!-- Top Greeting & Real-time Toast -->
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight">Bonjour, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">{{ Auth::user()->name }}</span> !</h1>
            <p class="text-gray-500 mt-2 font-medium">Voici le résumé de votre activité de cotisation.</p>
        </div>

        <template x-if="notification">
            <div x-transition class="fixed top-28 right-8 bg-[#161925] border border-green-500/30 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 flex items-center gap-4 animate-in fade-in slide-in-from-right-4 duration-500">
                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="font-bold text-sm">Action Réussie</p>
                    <p class="text-xs text-gray-400" x-text="notification"></p>
                </div>
            </div>
        </template>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- SOLDE (Balance) Card -->
        <div class="relative group overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-700 opacity-90 rounded-[2rem] transition-transform duration-500 group-hover:scale-105"></div>
            <div class="relative p-8 h-full flex flex-col justify-between min-h-[160px]">
                <div class="flex items-center justify-between opacity-80">
                    <span class="text-sm font-bold uppercase tracking-widest text-white/70">Mon Solde Actuel</span>
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="mt-4">
                    <p class="text-4xl font-black text-white leading-none">{{ number_format($solde, 0, ',', ' ') }} <small class="text-lg font-normal opacity-50">FCFA</small></p>
                </div>
            </div>
        </div>

        <!-- TOTAL VERSÉ -->
        <div class="bg-[#161925] border border-white/5 rounded-[2rem] p-8 flex flex-col justify-between group hover:border-indigo-500/30 transition-all duration-300">
            <div class="flex items-center justify-between text-gray-500 group-hover:text-indigo-400 transition-colors">
                <span class="text-sm font-bold uppercase tracking-widest">Total Versé</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
            </div>
            <div class="mt-4 text-white">
                <p class="text-2xl font-bold">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</p>
                <p class="text-xs text-gray-500 mt-1 uppercase font-bold tracking-widest leading-none">Historique complet</p>
            </div>
        </div>

        <!-- TOTAL RETIRÉ -->
        <div class="bg-[#161925] border border-white/5 rounded-[2rem] p-8 flex flex-col justify-between group hover:border-pink-500/30 transition-all duration-300">
            <div class="flex items-center justify-between text-gray-500 group-hover:text-pink-400 transition-colors">
                <span class="text-sm font-bold uppercase tracking-widest">Total Retiré</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
            </div>
            <div class="mt-4 text-white">
                <p class="text-2xl font-bold">{{ number_format($totalRetires, 0, ',', ' ') }} FCFA</p>
                <p class="text-xs text-rose-500/50 mt-1 uppercase font-bold tracking-widest leading-none">Sommes décaissées</p>
            </div>
        </div>
    </div>

    <!-- History Table Container -->
    <div class="bg-[#161925] border border-white/5 rounded-[2rem] overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
            <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-3">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Activités & Mouvements
            </h3>
            <span class="px-4 py-1 bg-indigo-500/10 text-indigo-400 text-xs font-bold rounded-full border border-indigo-500/20">
                {{ $cotisations->count() }} Opérations
            </span>
        </div>

        <div class="overflow-x-auto p-4">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Montant</th>
                        <th class="px-6 py-4">Enregistré par</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($cotisations as $cotisation)
                        <tr class="hover:bg-white/[0.03] transition-colors group">
                            <td class="px-6 py-5 text-sm font-semibold text-gray-300">
                                {{ $cotisation->date_paiement->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-5">
                                @if($cotisation->type == 'versement')
                                    <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase rounded-lg border border-indigo-500/20">Versement</span>
                                @else
                                    <span class="px-3 py-1 bg-pink-500/10 text-pink-400 text-[10px] font-black uppercase rounded-lg border border-pink-500/20">Retrait</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-md font-extrabold @if($cotisation->type == 'versement') text-white @else text-pink-400 @endif">
                                {{ $cotisation->type == 'retrait' ? '-' : '+' }} {{ number_format($cotisation->montant, 0, ',', ' ') }} <span class="text-xs font-normal text-gray-500">FCFA</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-white/5 flex items-center justify-center text-[10px] text-gray-500">
                                        {{ substr($cotisation->collecteur->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-400">{{ $cotisation->collecteur->name ?? 'Admin' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center opacity-30">
                                <div class="flex flex-col items-center gap-4">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"></path></svg>
                                    <p class="text-lg font-bold">Aucune opération trouvée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>