<?php

namespace App\Observers;

use App\Jobs\Search\ReindexModelJob;
use App\Models\User;

class UserObserver
{
    public function saved(User $user): void
    {
        if (! $user->roles()->count() > 1) {
            $role = $user->roles()->latest()->first();
            $user->syncRoles([$role]);
        }

        ReindexModelJob::dispatch(User::class, [$user->id]);
    }

    public function updated(User $user): void
    {
        if (! $user->roles()->count() > 1) {
            $role = $user->roles()->latest()->first();
            $user->syncRoles([$role]);
        }
    }

    public function deleted(User $user): void
    {
        $user->roles()->detach();
        $user->permissions()->detach();
        $user->unsearchable();
    }
}
