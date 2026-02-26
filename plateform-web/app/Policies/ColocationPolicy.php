<?php

namespace App\Policies;

use App\Models\Colocation;
use App\Models\User;

class ColocationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Colocation $colocation): bool
    {
        return $colocation->isOwner($user) || $colocation->isMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Colocation $colocation): bool
    {
        return $colocation->isOwner($user);
    }

    public function delete(User $user, Colocation $colocation): bool
    {
        return $colocation->isOwner($user);
    }

    public function addMember(User $user, Colocation $colocation): bool
    {
        return $colocation->isOwner($user);
    }

    public function removeMember(User $user, Colocation $colocation): bool
    {
        return $colocation->isOwner($user);
    }

    public function leave(User $user, Colocation $colocation): bool
    {
        return $colocation->isMember($user) && !$colocation->isOwner($user);
    }
}
