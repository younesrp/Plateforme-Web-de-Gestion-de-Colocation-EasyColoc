<x-app-layout>
    <div class="min-h-screen bg-[#0B1224] text-slate-300 pb-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <a href="{{ route('colocations.show', $colocation) }}" class="text-slate-400 hover:text-white text-sm">
                        ← Retour
                    </a>
                    <h2 class="text-3xl font-black text-white mt-2">{{ $colocation->name }}</h2>
                </div>
                <a href="{{ route('payments.index', $colocation) }}" class="px-4 py-2 bg-slate-700 text-white text-sm font-medium rounded-xl hover:bg-slate-600 transition-all">
                    Historique
                </a>
            </div>

            <div class="bg-slate-800/40 rounded-3xl border border-white/5 p-8">
                <h3 class="text-xl font-bold text-white mb-6">Qui doit à qui ?</h3>
                
                <div class="space-y-3">
                    @forelse($balances['settlements'] as $settlement)
                        <div class="flex items-center justify-between p-4 bg-slate-900/50 rounded-xl border border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-rose-500/20 rounded-full flex items-center justify-center">
                                    <span class="text-rose-400 font-bold text-sm">{{ substr($settlement['from']->name, 0, 1) }}</span>
                                </div>
                                <span class="text-white font-medium">{{ $settlement['from']->name }}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-2xl font-black text-rose-400">{{ number_format($settlement['amount'], 2) }} €</span>
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-white font-medium">{{ $settlement['to']->name }}</span>
                                <div class="w-10 h-10 bg-emerald-500/20 rounded-full flex items-center justify-center">
                                    <span class="text-emerald-400 font-bold text-sm">{{ substr($settlement['to']->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('payments.store', $colocation) }}">
                                @csrf
                                <input type="hidden" name="from_user_id" value="{{ $settlement['from']->id }}">
                                <input type="hidden" name="to_user_id" value="{{ $settlement['to']->id }}">
                                <input type="hidden" name="amount" value="{{ $settlement['amount'] }}">
                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-500 transition-all">
                                    Marquer payé
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-slate-400">Tout le monde est à jour !</p>
                        </div>
                    @endforelse
                </div>

                @if(count($balances['balances']) > 0)
                <div class="mt-8 pt-8 border-t border-white/5">
                    <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Soldes individuels</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($balances['balances'] as $balance)
                            <div class="p-4 bg-slate-900/30 rounded-xl">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-300">{{ $balance['user']->name }}</span>
                                    <span class="font-bold {{ $balance['balance'] > 0 ? 'text-emerald-400' : ($balance['balance'] < 0 ? 'text-rose-400' : 'text-slate-400') }}">
                                        {{ $balance['balance'] > 0 ? '+' : '' }}{{ number_format($balance['balance'], 2) }} €
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
