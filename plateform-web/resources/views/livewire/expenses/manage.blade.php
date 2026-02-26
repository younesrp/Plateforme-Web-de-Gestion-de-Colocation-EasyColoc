<?php

use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public Colocation $colocation;
    public string $title = '';
    public string $amount = '';
    public string $date = '';
    public ?int $category_id = null;
    public string $description = '';
    public ?string $month = null;

    public function mount(Colocation $colocation): void
    {
        $this->colocation = $colocation;
        $this->date = now()->format('Y-m-d');
    }

    public function save(): void
    {
        $this->authorize('view', $this->colocation);

        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
        ]);

        Expense::create([
            'colocation_id' => $this->colocation->id,
            'payer_id' => Auth::id(),
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
        ]);

        $this->reset(['title', 'amount', 'description', 'category_id']);
        $this->date = now()->format('Y-m-d');
    }

    public function delete(int $id): void
    {
        $expense = Expense::findOrFail($id);
        
        if ($expense->payer_id !== Auth::id() && !$this->colocation->isOwner(Auth::user())) {
            abort(403);
        }

        $expense->delete();
    }

    public function with(): array
    {
        $query = $this->colocation->expenses()->with(['payer', 'category']);

        if ($this->month) {
            $query->whereYear('date', substr($this->month, 0, 4))
                  ->whereMonth('date', substr($this->month, 5, 2));
        }

        $expenses = $query->latest('date')->get();

        $stats = $this->colocation->expenses()
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->category?->name ?? 'Sans catégorie' => $item->total
            ]);

        return [
            'expenses' => $expenses,
            'categories' => $this->colocation->categories,
            'stats' => $stats,
        ];
    }
}; ?>

<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Dépenses</h3>

    <form wire:submit="save" class="mb-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="title" value="Titre" />
                <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="amount" value="Montant (€)" />
                <x-text-input wire:model="amount" id="amount" class="block mt-1 w-full" type="number" step="0.01" required />
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="date" value="Date" />
                <x-text-input wire:model="date" id="date" class="block mt-1 w-full" type="date" required />
                <x-input-error :messages="$errors->get('date')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="category_id" value="Catégorie" />
                <select wire:model="category_id" id="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">Sans catégorie</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <x-input-label for="description" value="Description (optionnel)" />
            <textarea wire:model="description" id="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="2"></textarea>
        </div>

        <x-primary-button>Ajouter la dépense</x-primary-button>
    </form>

    <div class="mb-4 flex justify-between items-center">
        <div>
            <x-input-label for="month" value="Filtrer par mois" />
            <input wire:model.live="month" id="month" type="month" class="mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" />
        </div>
    </div>

    @if($stats->isNotEmpty())
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded">
            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Statistiques par catégorie</h4>
            <div class="space-y-1">
                @foreach($stats as $name => $total)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $name }}</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($total, 2) }} €</span>
                    </div>
                @endforeach
                <div class="flex justify-between text-sm font-bold border-t pt-1 mt-2">
                    <span class="text-gray-900 dark:text-gray-100">Total</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ number_format($stats->sum(), 2) }} €</span>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-3">
        @forelse($expenses as $expense)
            <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            @if($expense->category)
                                <div class="w-4 h-4 rounded" style="background-color: {{ $expense->category->color }}"></div>
                            @endif
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $expense->title }}</h4>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Payé par {{ $expense->payer->name }} le {{ $expense->date->format('d/m/Y') }}
                        </p>
                        @if($expense->description)
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $expense->description }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($expense->amount, 2) }} €</p>
                        @if($expense->payer_id === Auth::id() || $colocation->isOwner(Auth::user()))
                            <button wire:click="delete({{ $expense->id }})" wire:confirm="Supprimer cette dépense ?" class="text-xs text-red-600 dark:text-red-400 hover:underline mt-1">
                                Supprimer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune dépense</p>
        @endforelse
    </div>
</div>
