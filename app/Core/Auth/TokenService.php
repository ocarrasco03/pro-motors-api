<?php

namespace App\Core\Auth;

use App\Models\User;
use Illuminate\Http\Request;

interface TokenService
{
    /**
     * @param User $user
     * @return array
     */
    public function generateToken(User $user): array;

    /**
     * @param User $user
     * @return void
     */
    public function revokeToken(User $user): void;

    /**
     * @param User $user
     * @return void
     */
    public function revokeAllTokens(User $user): void;

    /**
     * @param Request $request
     * @return bool
     */
    public function verifyToken(Request $request): bool;
}
