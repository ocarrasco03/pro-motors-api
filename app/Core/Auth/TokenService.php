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
     * @return bool
     */
    public function revokeToken(User $user): bool;

    /**
     * @param User $user
     * @return bool
     */
    public function revokeAllTokens(User $user): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function verifyToken(Request $request): bool;
}
