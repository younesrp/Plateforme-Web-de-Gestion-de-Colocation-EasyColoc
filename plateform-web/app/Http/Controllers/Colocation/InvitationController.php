<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvitationController extends Controller
{
    use AuthorizesRequests;

    public function create(Colocation $colocation)
    {
        $this->authorize('update', $colocation);
        return view('invitations.send', compact('colocation'));
    }

    public function store(Request $request, Colocation $colocation)
    {
        $this->authorize('update', $colocation);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => $validated['email'],
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($validated['email'])->send(new InvitationMail($invitation));

        return back()->with('status', 'Invitation envoyée avec succès.');
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->firstOrFail();

        $invitation->load('colocation.owner');

        return view('invitations.show', compact('invitation'));
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->firstOrFail();

        if (Auth::user()->hasActiveColocation()) {
            return back()->withErrors(['error' => 'Vous avez déjà une colocation active.']);
        }

        $invitation->colocation->members()->attach(Auth::id());
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('colocations.show', $invitation->colocation)
            ->with('status', 'Vous avez rejoint la colocation avec succès.');
    }

    public function decline($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->firstOrFail();

        $invitation->delete();

        return redirect()->route('dashboard')
            ->with('status', 'Invitation refusée.');
    }
}
