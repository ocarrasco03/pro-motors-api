<?php

namespace App\Core\Auth;

use App\Http\Resources\Settings\UserProfileResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface AuthService
{
    public function login(array $data): array;
    public function register(Request $request): JsonResponse;
    public function logout(User $user): bool;
    public function refresh(User $user): array;
    public function me(User $user): UserProfileResource;
}
