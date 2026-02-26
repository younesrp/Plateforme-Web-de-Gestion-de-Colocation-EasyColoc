<?php

use App\Mail\InvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Component;

new class extends Component
{
    public Colocation $colocation;
    public string $email = '';

    public function mount(Colocation $colocation): void
    {
        $this->colocation = $colocation;
    }

    public function send(): void
    {
        $this->authorize('addMember', $this->colocation);

        $validated = $this->validate([
            'email' => 'required|email',
        ]);

        $invitation = Invitation::create([
            'colocation_id' => $this->colocation->id,
            'email' => $validated['email'],
            'token' => Invitation::generateToken(),
            'status' => 'pending',
        ]);

        Mail::to($validated['email'])->send(new InvitationMail($invitation));

        session()->flash('status', 'Invitation envoyée avec succès.');
        $this->email = '';
    }
}; ?>

<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Inviter un membre</h3>
    
    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="send">
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-primary-button>Envoyer l'invitation</x-primary-button>
        </div>
    </form>

    <div class="mt-6">
        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Invitations en attente</h4>
        @if($colocation->invitations()->where('status', 'pending')->count() > 0)
            <ul class="space-y-2">
                @foreach($colocation->invitations()->where('status', 'pending')->get() as $inv)
                    <li class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $inv->email }} - <span class="text-xs text-gray-500">{{ $inv->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune invitation en attente</p>
        @endif
    </div>
</div>
