<?php

use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public Colocation $colocation;

    public function mount(Colocation $colocation): void
    {
        $this->authorize('view', $colocation);
        $this->colocation = $colocation->load(['owner', 'activeMembers']);
    }

    public function cancel(): void
    {
        $this->authorize('delete', $this->colocation);

        $this->colocation->update(['status' => 'cancelled']);

        session()->flash('status', 'Colocation annulée avec succès.');
        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Description</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $colocation->description ?? 'Aucune description' }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Propriétaire</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $colocation->owner->name }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Membres actifs</h3>
                    <ul class="mt-2 space-y-2">
                        @foreach($colocation->activeMembers as $member)
                            <li class="text-gray-600 dark:text-gray-400">
                                {{ $member->name }} 
                                @if($member->id === $colocation->owner_id)
                                    <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">Owner</span>
                                @endif
                                <span class="text-xs text-gray-500">(Réputation: {{ $member->reputation }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $colocation->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        {{ ucfirst($colocation->status) }}
                    </span>
                </div>

                @can('delete', $colocation)
                    @if($colocation->isActive())
                        <div class="mt-6">
                            <x-danger-button wire:click="cancel" wire:confirm="Êtes-vous sûr de vouloir annuler cette colocation ?">
                                Annuler la colocation
                            </x-danger-button>
                        </div>
                    @endif
                @endcan

                <div class="mt-6">
                    <livewire:colocations.leave-button :colocation="$colocation" />
                </div>

                @can('addMember', $colocation)
                    @if($colocation->isActive())
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <livewire:invitations.send :colocation="$colocation" />
                        </div>
                    @endif
                @endcan

                @if($colocation->isActive())
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <livewire:categories.manage :colocation="$colocation" />
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <livewire:expenses.manage :colocation="$colocation" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
