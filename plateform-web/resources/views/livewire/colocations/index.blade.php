<?php

use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'colocations' => Auth::user()->colocations()->with('owner')->latest()->get(),
            'hasActiveColocation' => Auth::user()->hasActiveColocation(),
        ];
    }
}; ?>

<div>
    <div class="mb-4 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mes Colocations</h3>
        @if(!$hasActiveColocation)
            <a href="{{ route('colocations.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Créer une colocation
            </a>
        @else
            <span class="text-sm text-gray-500 dark:text-gray-400">Vous avez déjà une colocation active</span>
        @endif
    </div>

    @if($colocations->isEmpty())
        <p class="text-gray-600 dark:text-gray-400">Vous n'avez aucune colocation.</p>
    @else
        <div class="space-y-4">
            @foreach($colocations as $colocation)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <a href="{{ route('colocations.show', $colocation) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                    {{ $colocation->name }}
                                </a>
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $colocation->description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                Propriétaire: {{ $colocation->owner->name }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ $colocation->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ ucfirst($colocation->status) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
