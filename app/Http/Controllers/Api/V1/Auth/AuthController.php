<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Application\Services\Auth\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        return $this->success($this->authService->login($request->validated()));
    }

    public function logout(Request $request)
    {
        if ($this->authService->logout($request->user())) {
            return $this->success(null, 'You have been logged out.');
        }

        return $this->error('Something went wrong. Please try again later.');
    }

    public function refresh(Request $request)
    {
        return $this->success(
            $this->authService->refresh($request->user()),
            'Token successfully refreshed.'
        );
    }

    public function me(Request $request)
    {
        return $this->success(
            $this->authService->me($request->user()),
            'User profile retrieved successfully.');
    }
}
