<?php

namespace App\Providers;

use App\Core\Auth\AuthServiceImpl;
use App\Core\Auth\AuthService;
use App\Core\Auth\TokenServiceImpl;
use App\Core\Auth\TokenService;
use Illuminate\Support\ServiceProvider;

class ProMotorsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $bindings = [
            TokenService::class => TokenServiceImpl::class,
            AuthService::class  => AuthServiceImpl::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
