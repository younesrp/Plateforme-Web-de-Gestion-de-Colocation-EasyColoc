<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $colocation->name }}
            </h2>
            <div class="flex gap-2">
                @can('update', $colocation)
                    <a href="{{ route('invitations.create', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Inviter un membre
                    </a>
                @endcan
                @can('leave', $colocation)
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir quitter cette colocation ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                            Quitter
                        </button>
                    </form>
                @endcan
                @can('delete', $colocation)
                    <form method="POST" action="{{ route('colocations.destroy', $colocation) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette colocation ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            Annuler
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informations</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $colocation->description ?? 'Pas de description' }}</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Propriétaire: {{ $colocation->owner->name }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Membres ({{ $colocation->activeMembers->count() }})</h3>
                <div class="space-y-2">
                    @foreach($colocation->activeMembers as $member)
                        @php
                            $balance = $balances['balances'][$member->id]['balance'] ?? 0;
                        @endphp
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div class="flex items-center gap-3">
                                @if($balance < -0.01)
                                    <span class="px-2 py-1 text-xs font-bold bg-red-100 text-red-800 rounded">{{ number_format(abs($balance), 2) }} € à donner</span>
                                @elseif($balance > 0.01)
                                    <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded">{{ number_format($balance, 2) }} € à recevoir</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-800 rounded">À jour</span>
                                @endif
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</span>
                                <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">⭐ {{ $member->reputation }} pts</span>
                                @if($colocation->isOwner($member))
                                    <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Propriétaire</span>
                                @endif
                            </div>
                            @can('removeMember', $colocation)
                                @if(!$colocation->isOwner($member))
                                    <form method="POST" action="{{ route('members.remove', [$colocation, $member]) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer ce membre ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Retirer</button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Qui doit à qui ?</h3>
                
                <div class="space-y-3">
                    @forelse($balances['settlements'] as $settlement)
                        <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded">
                            <div class="flex-1">
                                <p class="text-gray-900 dark:text-gray-100">
                                    <span class="font-medium">{{ $settlement['from']->name }}</span>
                                    doit
                                    <span class="font-bold text-lg text-red-600">{{ number_format($settlement['amount'], 2) }} €</span>
                                    à
                                    <span class="font-medium">{{ $settlement['to']->name }}</span>
                                </p>
                            </div>
                            @can('update', $colocation)
                                <form method="POST" action="{{ route('payments.store', $colocation) }}">
                                    @csrf
                                    <input type="hidden" name="from_user_id" value="{{ $settlement['from']->id }}">
                                    <input type="hidden" name="to_user_id" value="{{ $settlement['to']->id }}">
                                    <input type="hidden" name="amount" value="{{ $settlement['amount'] }}">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                                        Marquer payé
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">Tout le monde est à jour !</p>
                    @endforelse
                </div>

                <div class="mt-4 text-right">
                    <a href="{{ route('payments.index', $colocation) }}" class="text-blue-600 hover:text-blue-800 text-sm">Voir l'historique des paiements</a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dépenses</h3>
                    <a href="{{ route('categories.index', $colocation) }}" class="text-blue-600 hover:text-blue-800">Gérer catégories</a>
                </div>

                <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="mb-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="title" value="Titre" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="amount" value="Montant" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="date" value="Date" />
                            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="date('Y-m-d')" required />
                        </div>
                        <div>
                            <x-input-label for="category_id" value="Catégorie" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Aucune</option>
                                @foreach($colocation->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                    </div>
                    <x-primary-button>Ajouter une dépense</x-primary-button>
                </form>

                <div class="space-y-2">
                    @forelse($colocation->expenses()->latest()->take(10)->get() as $expense)
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $expense->title }}</span>
                                <span class="ml-2 text-gray-600 dark:text-gray-400">{{ number_format($expense->amount, 2) }} €</span>
                                <span class="ml-2 text-sm text-gray-500">par {{ $expense->payer->name }}</span>
                            </div>
                            @if($expense->payer_id === auth()->id() || $colocation->isOwner(auth()->user()))
                                <form method="POST" action="{{ route('expenses.destroy', [$colocation, $expense]) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucune dépense pour le moment</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
