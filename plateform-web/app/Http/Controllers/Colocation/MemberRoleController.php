<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MemberRoleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private ReputationService $reputationService) {}

    public function promoteToOwner(Request $request, Colocation $colocation, User $user)
    {
        $this->authorize('update', $colocation);

        if (!$colocation->isMember($user)) {
            return back()->withErrors(['error' => 'Cet utilisateur n\'est pas membre de la colocation.']);
        }

        if ($colocation->isOwner($user)) {
            return back()->withErrors(['error' => 'Cet utilisateur est déjà propriétaire.']);
        }

        $colocation->update(['owner_id' => $user->id]);

        return back()->with('status', 'Le membre a été promu propriétaire avec succès.');
    }

    public function removeMember(Request $request, Colocation $colocation, User $user)
    {
        $this->authorize('removeMember', $colocation);

        if ($colocation->isOwner($user)) {
            return back()->withErrors(['error' => 'Le propriétaire ne peut pas être retiré.']);
        }

        if (!$colocation->isMember($user)) {
            return back()->withErrors(['error' => 'Cet utilisateur n\'est pas membre de la colocation.']);
        }

        $this->reputationService->transferDebtOnRemoval($colocation, $user);

        $colocation->members()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        return back()->with('status', 'Le membre a été retiré avec succès.');
    }
}
