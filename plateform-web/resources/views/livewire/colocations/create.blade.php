<?php

use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $description = '';

    public function create(): void
    {
        if (Auth::user()->hasActiveColocation()) {
            $this->addError('name', 'Vous avez déjà une colocation active.');
            return;
        }

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $colocation = Colocation::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => Auth::id(),
            'status' => 'active',
        ]);

        $colocation->members()->attach(Auth::id());

        $this->redirect(route('colocations.show', $colocation), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="create">
        <div>
            <x-input-label for="name" value="Nom de la colocation" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="description" value="Description" />
            <textarea wire:model="description" id="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3"></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>Créer la colocation</x-primary-button>
        </div>
    </form>
</div>
