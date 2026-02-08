<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function saved(User $user): void
    {
        if (! $user->roles()->count() > 1) {
            $role = $user->roles()->latest()->first();
            $user->syncRoles([$role]);
        }
    }

    public function updated(User $user): void
    {
        if (! $user->roles()->count() > 1) {
            $role = $user->roles()->latest()->first();
            $user->syncRoles([$role]);
        }
    }
}
