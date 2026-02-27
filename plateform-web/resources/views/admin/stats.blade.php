<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <h1 class="text-2xl font-bold text-gray-900">Administration</h1>
                <p class="text-sm text-gray-500">Statistiques et gestion des utilisateurs</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Utilisateurs</p>
                            <h3 class="text-4xl font-bold text-indigo-600 mt-2">{{ $totalUsers }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Colocations</p>
                            <h3 class="text-4xl font-bold text-purple-600 mt-2">{{ $totalColocations }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Dépenses</p>
                            <h3 class="text-4xl font-bold text-emerald-600 mt-2">{{ number_format($totalExpenses, 0) }} €</h3>
                        </div>
                        <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Management -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Gestion des Utilisateurs ({{ $users->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 {{ $user->is_banned ? 'bg-red-100' : 'bg-gradient-to-br from-indigo-500 to-purple-500' }} rounded-full flex items-center justify-center">
                                    <span class="{{ $user->is_banned ? 'text-red-600' : 'text-white' }} font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                                        @if($user->isAdmin())
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Admin</span>
                                        @endif
                                        @if($user->is_banned)
                                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Banni</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $user->email }} • ⭐ {{ $user->reputation }} pts</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if(!$user->isAdmin())
                                    @if($user->is_banned)
                                        <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                                Débannir
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.ban', $user) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir bannir cet utilisateur ?');">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                                Bannir
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">Protégé</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Banned Users -->
            @if($bannedUsers->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Utilisateurs Bannis ({{ $bannedUsers->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($bannedUsers as $user)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <span class="text-red-600 font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">Réputation: {{ $user->reputation }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
