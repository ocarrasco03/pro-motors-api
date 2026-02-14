<?php

namespace App\Application\Services\Auth;

use App\Domain\ValueObjects\Enums\StatusEnum;
use App\Http\Resources\Settings\UserProfileResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthServiceImpl implements AuthService
{
    public function __construct(protected TokenService $tokenService) {}

    public function login(array $data): array
    {
        $user = User::where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password) || ! $user->active) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->company && $user->company->status !== StatusEnum::ACTIVE) {
            throw ValidationException::withMessages([
                'login' => ['The company is not active.'],
            ]);
        }

        $activeSessions = $user->tokens()->count();
        $maxSessions = config('auth.max_sessions', 5);

        if ($activeSessions >= $maxSessions) {
            $user->tokens()->oldest()->first()->delete();
        }

        return $this->tokenService->generateToken($user);
    }

    public function logout(User $user): bool
    {
        return $this->tokenService->revokeToken($user);
    }

    public function refresh(User $user): array
    {
        $this->tokenService->revokeToken($user);

        return $this->tokenService->generateToken($user);
    }

    public function me(User $user): UserProfileResource
    {
        return new UserProfileResource($user->load([
            'company:id,name,slug',
            'roles:id,name',
        ])
            ->loadMissing('permissions'));
    }
}
