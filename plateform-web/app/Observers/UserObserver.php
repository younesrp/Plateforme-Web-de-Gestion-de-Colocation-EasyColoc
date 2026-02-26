<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        if (User::count() === 1) {
            $user->role = 'admin';
            $user->save();
        }
    }
}
