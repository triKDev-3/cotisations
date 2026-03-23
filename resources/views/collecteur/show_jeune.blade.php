<x-app-layout>
    <x-slot name="header">
        <span class="text-emerald-500">Détails Membre</span>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        <!-- Member Header Card -->
        <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] p-8 lg:p-12 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-600/10 blur-[100px] -z-10 rounded-full"></div>
            
            <div class="flex flex-col lg:flex-row items-center justify-between gap-10">
                <div class="flex items-center gap-8">
                    <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center text-white text-4xl font-black shadow-2xl shadow-emerald-500/20">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-white tracking-tighter italic">{{ $user->name }}</h1>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="px-4 py-1.5 bg-white/5 border border-white/10 rounded-full text-[10px] font-black text-emerald-400 uppercase tracking-widest italic">
                                {{ $user->numero_compte }}
                            </span>
                            <span class="text-gray-500 text-sm font-medium">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('collecteur.jeunes.edit', $user) }}" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg shadow-emerald-500/10 flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Modifier Profil
                    </a>
                    <a href="{{ route('collecteur.dashboard') }}" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-2xl transition-all font-bold uppercase tracking-widest text-xs border border-white/5">
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Balance Card -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-[2.5rem] p-8 shadow-2xl shadow-emerald-900/20 group relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <p class="text-white/60 text-xs font-black uppercase tracking-widest italic">Solde Actuel</p>
                <h3 class="text-4xl lg:text-5xl font-black text-white mt-4 italic tracking-tighter">{{ number_format($solde, 0, ',', ' ') }} <span class="text-xl">F</span></h3>
            </div>

            <!-- Total In Card -->
            <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] p-8 shadow-xl">
                <p class="text-gray-500 text-xs font-black uppercase tracking-widest italic flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Total Versé
                </p>
                <h3 class="text-3xl font-black text-white mt-4 italic tracking-tighter">{{ number_format($totalPaye, 0, ',', ' ') }} <span class="text-lg">F</span></h3>
            </div>

            <!-- Total Out Card -->
            <div class="bg-[#161925] border border-white/5 rounded-[2.5rem] p-8 shadow-xl">
                <p class="text-gray-500 text-xs font-black uppercase tracking-widest italic flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                    Total Retiré
                </p>
                <h3 class="text-3xl font-black text-white mt-4 italic tracking-tighter text-gray-400">{{ number_format($totalRetires, 0, ',', ' ') }} <span class="text-lg">F</span></h3>
            </div>
        </div>

        <!-- Transactions History -->
        <div class="bg-[#161925] border border-white/5 rounded-[3rem] p-8 lg:p-12 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500/0 via-emerald-500/40 to-emerald-500/0"></div>
            
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-2xl font-black text-white tracking-tight italic">Historique des Transactions</h2>
                <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                    {{ $cotisations->count() }} Opérations
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] border-b border-white/5">
                            <th class="pb-6">Date d'effet</th>
                            <th class="pb-6">Type d'opération</th>
                            <th class="pb-6">N° Transaction</th>
                            <th class="pb-6 text-right uppercase">Montant (CFA)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($cotisations as $cotisation)
                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                <td class="py-6 pr-6">
                                    <span class="text-sm font-bold text-gray-300">{{ $cotisation->date_paiement->format('d M Y') }}</span>
                                </td>
                                <td class="py-6">
                                    @if($cotisation->type === 'versement')
                                        <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">IN</span>
                                    @else
                                        <span class="px-3 py-1 bg-pink-500/10 border border-pink-500/20 rounded-lg text-[10px] font-black text-pink-500 uppercase tracking-widest italic">OUT</span>
                                    @endif
                                </td>
                                <td class="py-6 pr-6">
                                    <span class="text-xs font-medium text-gray-500 font-mono">TRX-{{ str_pad($cotisation->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="py-6 text-right">
                                    <span class="text-lg font-black @if($cotisation->type === 'versement') text-white @else text-pink-500 @endif italic tracking-tighter">
                                        @if($cotisation->type === 'retrait')-@endif{{ number_format($cotisation->montant, 0, ',', ' ') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-gray-600 mb-6">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <p class="text-lg font-bold text-gray-500 italic">Aucune transaction trouvée</p>
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
