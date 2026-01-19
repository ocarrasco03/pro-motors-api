<?php

namespace App\Core\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TokenServiceImpl implements TokenService
{
    public function generateToken(User $user): array
    {
        $token      = $user->createToken(
            config('app.name')
        );
        $plainToken = $token->plainTextToken;
        $model      = $token->accessToken;

        $model->expires_at = now()->addMinutes(config('session.lifetime'));
        $model->save();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return [
            'access_token'  => $plainToken,
            'expires_at'    => $model->expires_at,
        ];
    }

    public function revokeToken(User $user): void
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function verifyToken(Request $request): bool
    {
        $user = $request->user();
        if (!$user) {
            return false;
        }

        $token = $user->currentAccessToken();

        if (!$token || !$token->expires_at) {
            return false;
        }

        return $token->expires_at->isPast();
    }
}
