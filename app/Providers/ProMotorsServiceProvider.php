<?php

namespace App\Providers;

use App\Core\Auth\AuthServiceImpl;
use App\Core\Auth\AuthService;
use App\Core\Auth\TokenServiceImpl;
use App\Core\Auth\TokenService;
use App\Core\Catalogs\ProductService;
use App\Core\Catalogs\ProductServiceImpl;
use App\Core\Settings\CompanyService;
use App\Core\Settings\CompanyServiceImpl;
use App\Core\Settings\UserService;
use App\Core\Settings\UserServiceImpl;
use Illuminate\Support\ServiceProvider;

class ProMotorsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $bindings = [
            // Auth services
            TokenService::class => TokenServiceImpl::class,
            AuthService::class  => AuthServiceImpl::class,

            // Catalogs services
            ProductService::class => ProductServiceImpl::class,

            // Settings services
            CompanyService::class => CompanyServiceImpl::class,
            UserService::class => UserServiceImpl::class,
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
