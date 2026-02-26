<?php

namespace App\Policies;

use App\Models\User;

class BasePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isBanned()) {
            return false;
        }

        return null;
    }
}
