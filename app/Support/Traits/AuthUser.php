<?php

namespace App\Support\Traits;

use App\Models\User;

trait AuthUser
{
    protected ?User $authUser = null;

    protected function authUser(): User
    {
        if ($this->authUser === null) {
            $this->authUser = auth()->user();

            if (! $this->authUser) {
                throw new \RuntimeException('Authenticated user is required.');
            }
        }

        return $this->authUser;
    }
}
