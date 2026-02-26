<?php

use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Invitation $invitation;
    public ?string $error = null;

    public function mount(string $token): void
    {
        $this->invitation = Invitation::where('token', $token)->firstOrFail();
        $this->invitation->load('colocation.owner');

        if (!$this->invitation->isPending()) {
            $this->error = 'Cette invitation n\'est plus valide.';
        }

        if (Auth::user()->email !== $this->invitation->email) {
            $this->error = 'Cette invitation n\'est pas pour vous.';
        }

        if (Auth::user()->hasActiveColocation()) {
            $this->error = 'Vous avez déjà une colocation active.';
        }
    }

    public function accept(): void
    {
        if ($this->error) {
            return;
        }

        if (Auth::user()->email !== $this->invitation->email) {
            $this->error = 'Cette invitation n\'est pas pour vous.';
            return;
        }

        if (Auth::user()->hasActiveColocation()) {
            $this->error = 'Vous avez déjà une colocation active.';
            return;
        }

        $this->invitation->accept();
        $this->invitation->colocation->members()->attach(Auth::id());

        session()->flash('status', 'Vous avez rejoint la colocation avec succès.');
        $this->redirect(route('colocations.show', $this->invitation->colocation), navigate: true);
    }

    public function refuse(): void
    {
        $this->invitation->refuse();

        session()->flash('status', 'Invitation refusée.');
        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Invitation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($error)
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
                        {{ $error }}
                    </div>
                    <a href="{{ route('dashboard') }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">
                        Retour au dashboard
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Invitation à rejoindre une colocation
                    </h3>

                    <div class="mb-6">
                        <p class="text-gray-600 dark:text-gray-400">
                            Vous avez été invité(e) à rejoindre la colocation :
                        </p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $invitation->colocation->name }}
                        </p>
                        @if($invitation->colocation->description)
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                {{ $invitation->colocation->description }}
                            </p>
                        @endif
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                            Propriétaire : {{ $invitation->colocation->owner->name }}
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <x-primary-button wire:click="accept">
                            Accepter l'invitation
                        </x-primary-button>
                        <x-danger-button wire:click="refuse">
                            Refuser
                        </x-danger-button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
