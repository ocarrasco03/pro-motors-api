<?php

namespace App\Application\Services\Auth;

use App\Http\Resources\Settings\UserProfileResource;
use App\Models\User;

interface AuthService
{
    public function login(array $data): array;

    public function logout(User $user): bool;

    public function refresh(User $user): array;

    public function me(User $user): UserProfileResource;
}
