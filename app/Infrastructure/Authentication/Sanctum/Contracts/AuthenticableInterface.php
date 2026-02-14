<?php

namespace App\Infrastructure\Authentication\Sanctum\Contracts;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

interface AuthenticableInterface
{
    public function createToken(User $user, string $name, array $abilities = ['*']): string;
    public function revokeToken(User $user, string $tokenId): bool;
    public function revokeAllTokens(User $user): bool;
    public function getCurrentToken(User $user): ?PersonalAccessToken;
}
