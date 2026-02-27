<x-app-layout>
    <div class="min-h-screen bg-[#0B1224] text-slate-300 pb-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6">
                <a href="{{ route('colocations.balances', $colocation) }}" class="text-slate-400 hover:text-white text-sm">
                    ← Retour aux soldes
                </a>
                <h2 class="text-3xl font-black text-white mt-2">Historique des paiements</h2>
                <p class="text-slate-400 text-sm mt-1">{{ $colocation->name }}</p>
            </div>

            <div class="bg-slate-800/40 rounded-3xl border border-white/5 p-8">
                <div class="space-y-3">
                    @forelse($payments as $payment)
                        <div class="flex items-center justify-between p-4 bg-slate-900/50 rounded-xl border border-white/5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium">
                                        {{ $payment->fromUser->name }} → {{ $payment->toUser->name }}
                                    </p>
                                    <p class="text-slate-500 text-xs">{{ $payment->date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black text-emerald-400">{{ number_format($payment->amount, 2) }} €</p>
                                <p class="text-slate-500 text-xs">{{ $payment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-slate-400">Aucun paiement enregistré</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
