<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Informations du profil
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Mettez à jour les informations de votre profil.
                            </p>
                        </header>

                        <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="name" value="Nom" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>Enregistrer</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Mettre à jour le mot de passe
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Assurez-vous d'utiliser un mot de passe long et aléatoire.
                            </p>
                        </header>

                        <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="current_password" value="Mot de passe actuel" />
                                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
                            </div>

                            <div>
                                <x-input-label for="password" value="Nouveau mot de passe" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>Enregistrer</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Supprimer le compte
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées.
                            </p>
                        </header>

                        <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?');">
                            @csrf
                            @method('DELETE')

                            <div>
                                <x-input-label for="password" value="Mot de passe" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                    Supprimer le compte
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
