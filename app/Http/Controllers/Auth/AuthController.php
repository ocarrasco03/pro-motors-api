<?php

namespace App\Http\Controllers\Auth;

use App\Core\Auth\AuthService as AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }

    public function refresh(Request $request)
    {
        return $this->authService->refresh($request);
    }

    public function me(Request $request)
    {
        return $this->authService->me($request);
    }
}
