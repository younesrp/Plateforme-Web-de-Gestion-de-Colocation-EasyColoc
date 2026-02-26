<?php

use App\Models\Colocation;
use App\Services\BalanceService;
use Livewire\Volt\Component;

new class extends Component
{
    public Colocation $colocation;

    public function mount(Colocation $colocation): void
    {
        $this->colocation = $colocation;
    }

    public function with(): array
    {
        $balanceService = new BalanceService();
        $data = $balanceService->calculateBalances($this->colocation);

        return [
            'balances' => $data['balances'],
            'settlements' => $data['settlements'],
            'total' => $data['total'],
        ];
    }
}; ?>

<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Balances et Remboursements</h3>

    @if(empty($balances))
        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune dépense pour calculer les balances</p>
    @else
        <div class="mb-6">
            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Soldes individuels</h4>
            <div class="space-y-2">
                @foreach($balances as $balance)
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $balance['user']->name }}</span>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    Payé: {{ number_format($balance['total_paid'], 2) }} € | 
                                    Part: {{ number_format($balance['share'], 2) }} €
                                </div>
                            </div>
                            <div class="text-right">
                                @if($balance['balance'] > 0.01)
                                    <span class="text-green-600 dark:text-green-400 font-bold">
                                        +{{ number_format($balance['balance'], 2) }} €
                                    </span>
                                    <div class="text-xs text-gray-500">À recevoir</div>
                                @elseif($balance['balance'] < -0.01)
                                    <span class="text-red-600 dark:text-red-400 font-bold">
                                        {{ number_format($balance['balance'], 2) }} €
                                    </span>
                                    <div class="text-xs text-gray-500">À payer</div>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400 font-bold">
                                        0.00 €
                                    </span>
                                    <div class="text-xs text-gray-500">Équilibré</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 p-3 bg-indigo-50 dark:bg-indigo-900 rounded">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Total des dépenses</span>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($total, 2) }} €</span>
                </div>
            </div>
        </div>

        @if(!empty($settlements))
            <div class="mt-6">
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Qui doit à qui ?</h4>
                <div class="space-y-3">
                    @foreach($settlements as $settlement)
                        <div class="p-4 bg-white dark:bg-gray-800 border-l-4 border-indigo-500 rounded shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $settlement['from']->name }}</span>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $settlement['to']->name }}</span>
                                </div>
                                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($settlement['amount'], 2) }} €
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-6 p-4 bg-green-50 dark:bg-green-900 rounded">
                <p class="text-green-700 dark:text-green-300 text-center font-medium">
                    ✓ Tous les comptes sont équilibrés !
                </p>
            </div>
        @endif
    @endif
</div>
