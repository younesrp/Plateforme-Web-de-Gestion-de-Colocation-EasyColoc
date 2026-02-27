<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\User;

class ReputationService
{
    public function __construct(private BalanceService $balanceService) {}

    public function updateOnLeave(Colocation $colocation, User $user): void
    {
        $balance = $this->getUserBalance($colocation, $user);
        
        if ($balance < -0.01) {
            $user->decrement('reputation');
        } else {
            $user->increment('reputation');
        }
    }

    public function transferDebtOnRemoval(Colocation $colocation, User $removedUser): void
    {
        $balance = $this->getUserBalance($colocation, $removedUser);
        
        if ($balance < -0.01) {
            $colocation->owner->decrement('reputation');
        }
    }

    private function getUserBalance(Colocation $colocation, User $user): float
    {
        $balances = $this->balanceService->calculateBalances($colocation);
        return $balances['balances'][$user->id]['balance'] ?? 0;
    }
}
