<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">EasyColoc</h1>
                            <p class="text-xs text-gray-500">Gestion de colocation simplifiée</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('colocations.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all">
                            + Nouvelle colocation
                        </a>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">⭐ {{ auth()->user()->reputation }} pts</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2">
                                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-sm font-medium text-gray-700">Mon profil</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.stats') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        <span class="text-sm font-medium text-gray-700">Administration</span>
                                    </a>
                                @endif
                                <hr class="my-2 border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 px-4 py-3 hover:bg-red-50 transition-colors w-full text-left">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        <span class="text-sm font-medium text-red-600">Déconnexion</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Mes colocations</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $colocations->count() }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Actives</p>
                            <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $colocations->where('status', 'active')->count() }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Statut</p>
                            <h3 class="text-3xl font-bold text-purple-600 mt-2">{{ auth()->user()->reputation }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Points de réputation</p>
                        </div>
                        <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colocations List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Mes colocations</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($colocations as $colocation)
                        <a href="{{ route('colocations.show', $colocation) }}" class="block px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($colocation->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $colocation->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $colocation->activeMembers->count() }} membres • Propriétaire: {{ $colocation->owner->name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($colocation->status === 'active')
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Active</span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">Inactive</span>
                                    @endif
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune colocation</h3>
                            <p class="text-gray-500 mb-6">Créez votre première colocation pour commencer</p>
                            <a href="{{ route('colocations.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                                + Créer une colocation
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>