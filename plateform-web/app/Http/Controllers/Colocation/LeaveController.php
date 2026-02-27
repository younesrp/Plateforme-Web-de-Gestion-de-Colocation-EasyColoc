<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Services\ReputationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeaveController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private ReputationService $reputationService) {}

    public function destroy(Colocation $colocation)
    {
        $this->authorize('leave', $colocation);

        if ($colocation->isOwner(Auth::user())) {
            return back()->withErrors(['error' => 'Le propriétaire ne peut pas quitter la colocation.']);
        }

        $user = Auth::user();
        $this->reputationService->updateOnLeave($colocation, $user);

        $colocation->members()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Vous avez quitté la colocation avec succès.');
    }
}
