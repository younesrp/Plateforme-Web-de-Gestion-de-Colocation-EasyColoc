<?php

use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public Colocation $colocation;

    public function mount(Colocation $colocation): void
    {
        $this->colocation = $colocation;
    }

    public function leave(): void
    {
        $this->authorize('leave', $this->colocation);

        if ($this->colocation->isOwner(Auth::user())) {
            $this->addError('leave', 'Le propriétaire ne peut pas quitter la colocation.');
            return;
        }

        $this->colocation->members()->updateExistingPivot(Auth::id(), [
            'left_at' => now(),
        ]);

        session()->flash('status', 'Vous avez quitté la colocation.');
        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div>
    @if(!$colocation->isOwner(Auth::user()) && $colocation->isActive())
        <x-danger-button wire:click="leave" wire:confirm="Êtes-vous sûr de vouloir quitter cette colocation ?">
            Quitter la colocation
        </x-danger-button>
        <x-input-error :messages="$errors->get('leave')" class="mt-2" />
    @endif
</div>
