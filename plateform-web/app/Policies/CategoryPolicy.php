<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        return $category->colocation->isMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Category $category): bool
    {
        return $category->colocation->isOwner($user);
    }

    public function delete(User $user, Category $category): bool
    {
        return $category->colocation->isOwner($user);
    }
}
