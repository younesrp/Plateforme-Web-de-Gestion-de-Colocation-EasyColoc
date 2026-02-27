<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-slate-900 rounded-3xl shadow-2xl relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-2xl font-bold text-white tracking-tight">Mes Colocations</h3>
            <p class="text-slate-400 text-sm">Gérez vos espaces en toute simplicité.</p>
        </div>
        <a href="{{ route('colocations.create') }}" class="relative z-10 inline-flex items-center px-6 py-3 bg-white text-slate-900 font-bold rounded-2xl hover:bg-primary-50 transition-all duration-300 shadow-lg hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Nouvelle Coloc
        </a>
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary-600/20 rounded-full blur-3xl"></div>
    </div>

    @if($colocations->isEmpty())
        <div class="group border-2 border-dashed border-slate-200 rounded-3xl p-12 text-center hover:border-primary-400 transition-colors">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-50 rounded-full mb-4 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-10 h-10 text-slate-300 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">C'est un peu vide hna...</h3>
            <p class="text-slate-500 mb-6">Créez votre première colocation pour commencer.</p>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($colocations as $colocation)
                <div class="group relative">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-primary-500 to-indigo-500 rounded-3xl blur opacity-0 group-hover:opacity-20 transition duration-500"></div>
                    
                    <a href="{{ route('colocations.show', $colocation) }}" class="relative block h-full bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-2xl transition-all duration-300">
                        <div class="flex justify-between items-start mb-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider {{ $colocation->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                <span class="w-2 h-2 rounded-full mr-2 {{ $colocation->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                                {{ $colocation->status }}
                            </span>
                            <div class="text-slate-300 group-hover:text-primary-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>
                        </div>

                        <h5 class="text-xl font-black text-slate-800 group-hover:text-primary-600 mb-2 transition-colors">
                            {{ $colocation->name }}
                        </h5>
                        
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 mb-6">
                            {{ $colocation->description ?? 'Pas de description encore.' }}
                        </p>

                        <div class="flex items-center pt-4 border-t border-slate-50">
                            <div class="flex -space-x-2 mr-3">
                                <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[10px] font-bold">
                                    {{ substr($colocation->owner->name, 0, 1) }}
                                </div>
                            </div>
                            <p class="text-xs font-semibold text-slate-400">Proprio: <span class="text-slate-700">{{ $colocation->owner->name }}</span></p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>