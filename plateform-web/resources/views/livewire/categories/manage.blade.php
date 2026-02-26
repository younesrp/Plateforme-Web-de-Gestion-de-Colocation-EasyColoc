<?php

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public Colocation $colocation;
    public string $name = '';
    public string $color = '#3B82F6';
    public ?int $editingId = null;

    public function mount(Colocation $colocation): void
    {
        $this->colocation = $colocation;
    }

    public function save(): void
    {
        $this->authorize('update', $this->colocation);

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        if ($this->editingId) {
            $category = Category::findOrFail($this->editingId);
            $this->authorize('update', $category);
            $category->update($validated);
        } else {
            Category::create([
                'colocation_id' => $this->colocation->id,
                'name' => $validated['name'],
                'color' => $validated['color'],
            ]);
        }

        $this->reset(['name', 'color', 'editingId']);
        $this->color = '#3B82F6';
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);
        
        $this->editingId = $id;
        $this->name = $category->name;
        $this->color = $category->color;
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);
        $category->delete();
    }

    public function cancel(): void
    {
        $this->reset(['name', 'color', 'editingId']);
        $this->color = '#3B82F6';
    }

    public function with(): array
    {
        return [
            'categories' => $this->colocation->categories()->latest()->get(),
        ];
    }
}; ?>

<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Catégories</h3>

    @can('update', $colocation)
        <form wire:submit="save" class="mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <x-input-label for="name" value="Nom" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="w-32">
                    <x-input-label for="color" value="Couleur" />
                    <input wire:model="color" id="color" type="color" class="block mt-1 w-full h-10 rounded border-gray-300 dark:border-gray-700" />
                </div>

                <div class="flex items-end gap-2">
                    <x-primary-button>{{ $editingId ? 'Modifier' : 'Ajouter' }}</x-primary-button>
                    @if($editingId)
                        <x-secondary-button type="button" wire:click="cancel">Annuler</x-secondary-button>
                    @endif
                </div>
            </div>
        </form>
    @endcan

    <div class="space-y-2">
        @forelse($categories as $category)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded" style="background-color: {{ $category->color }}"></div>
                    <span class="text-gray-900 dark:text-gray-100">{{ $category->name }}</span>
                </div>

                @can('update', $category)
                    <div class="flex gap-2">
                        <button wire:click="edit({{ $category->id }})" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                            Modifier
                        </button>
                        <button wire:click="delete({{ $category->id }})" wire:confirm="Supprimer cette catégorie ?" class="text-red-600 dark:text-red-400 hover:underline text-sm">
                            Supprimer
                        </button>
                    </div>
                @endcan
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune catégorie</p>
        @endforelse
    </div>
</div>
