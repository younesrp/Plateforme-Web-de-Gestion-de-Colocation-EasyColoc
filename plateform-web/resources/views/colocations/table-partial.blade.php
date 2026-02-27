<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/5">
                <th class="px-8 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Name</th>
                <th class="px-8 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Owner</th>
                <th class="px-8 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Status</th>
                <th class="px-8 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Updated</th>
                <th class="px-8 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-widest">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($colocations as $colocation)
            <tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors group">
                <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-blue-500/20">
                            {{ substr($colocation->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $colocation->name }}</p>
                            <p class="text-xs text-slate-500">{{ Str::limit($colocation->description ?? 'No description', 30) }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-8 py-5">
                    <p class="text-sm text-slate-300">{{ $colocation->owner->name }}</p>
                </td>
                <td class="px-8 py-5">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $colocation->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                        {{ ucfirst($colocation->status) }}
                    </span>
                </td>
                <td class="px-8 py-5">
                    <p class="text-xs text-slate-400">{{ $colocation->updated_at->diffForHumans() }}</p>
                </td>
                <td class="px-8 py-5 text-right">
                    <a href="{{ route('colocations.show', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-lg shadow-blue-600/20 group-hover:shadow-blue-600/40">
                        View
                        <svg class="w-3 h-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-8 py-12 text-center">
                    <p class="text-slate-500">No colocations found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
