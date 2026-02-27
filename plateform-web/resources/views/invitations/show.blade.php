<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Invitation à rejoindre une colocation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Vous êtes invité à rejoindre la colocation
                </h3>

                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $invitation->colocation->name }}</p>
                    <p class="mt-2 text-gray-700 dark:text-gray-300">{{ $invitation->colocation->description ?? 'Pas de description' }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Propriétaire: {{ $invitation->colocation->owner->name }}</p>
                </div>

                <div class="flex gap-4">
                    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Accepter l'invitation
                        </button>
                    </form>

                    <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            Refuser
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
