<?php

namespace App\Infrastructure\Authentication\Sanctum\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if (! $token) {
            throw new AuthenticationException('Token not provided');
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            throw new AuthenticationException('Token expired');
        }

        return $next($request);
    }
}
