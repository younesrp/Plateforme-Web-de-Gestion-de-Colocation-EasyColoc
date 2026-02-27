<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <h1 class="text-2xl font-bold text-gray-900">Gestion des Utilisateurs</h1>
                <p class="text-sm text-gray-500">Gérer les membres de la plateforme</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('status'))
                <div class="mb-6 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Tous les utilisateurs ({{ $users->count() }})</h2>
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
        </div>
    </div>
</x-app-layout>
