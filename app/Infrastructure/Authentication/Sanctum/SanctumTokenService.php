<?php

namespace App\Infrastructure\Authentication\Sanctum;

use App\Infrastructure\Authentication\Sanctum\Contracts\AuthenticableInterface;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenService implements AuthenticableInterface
{
    public function createToken(User $user, string $name, array $abilities = ['*']): string
    {
        $token = $user->createToken($name, $abilities);

        return $token->plainTextToken;
    }

    public function revokeToken(User $user, string $tokenId): bool
    {
        $token = PersonalAccessToken::findToken($tokenId);

        if (! $token || $token->tokenable_id !== $user->id) {
            return false;
        }

        return $token->delete();
    }

    public function revokeAllTokens(User $user): bool
    {
        return $user->tokens()->delete() > 0;
    }

    public function getCurrentToken(User $user): ?PersonalAccessToken
    {
        return $user->currentAccessToken();
    }

    public function isTokenValid(User $user, string $tokenId): bool
    {
        $token = PersonalAccessToken::findToken($tokenId);

        return $token && $token->tokenable_id === $user->id && ! $token->expires_at || $token->expires_at->isFuture();
    }
}
