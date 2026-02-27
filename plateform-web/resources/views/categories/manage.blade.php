<x-app-layout>
    <div class="min-h-screen bg-[#0B1224] text-slate-300 pb-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6">
                <a href="{{ route('colocations.show', $colocation) }}" class="text-slate-400 hover:text-white text-sm">
                    ← Retour
                </a>
                <h2 class="text-3xl font-black text-white mt-2">Gérer les catégories</h2>
                <p class="text-slate-400 text-sm mt-1">{{ $colocation->name }}</p>
            </div>

            @if(session('status'))
                <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-slate-800/40 rounded-3xl border border-white/5 p-8 mb-6">
                <h3 class="text-xl font-bold text-white mb-6">Ajouter une catégorie</h3>
                
                <form method="POST" action="{{ route('categories.store', $colocation) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nom</label>
                            <input name="name" type="text" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Couleur</label>
                            <input name="color" type="color" class="w-full h-11 bg-slate-900/50 border border-white/10 rounded-xl px-2 cursor-pointer" value="#3B82F6" required />
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-500 transition-all">
                        Ajouter
                    </button>
                </form>
            </div>

            <div class="bg-slate-800/40 rounded-3xl border border-white/5 p-8">
                <h3 class="text-xl font-bold text-white mb-6">Catégories existantes</h3>
                
                <div class="space-y-3">
                    @forelse($categories as $category)
                        <div class="flex justify-between items-center p-4 bg-slate-900/50 rounded-xl border border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg" style="background-color: {{ $category->color }}"></div>
                                <span class="font-medium text-white">{{ $category->name }}</span>
                            </div>
                            <form method="POST" action="{{ route('categories.destroy', [$colocation, $category]) }}" onsubmit="return confirm('Êtes-vous sûr ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-rose-400 hover:text-rose-300 font-medium transition-colors">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-slate-400 text-center py-8">Aucune catégorie</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
