<?php

namespace App\Providers;

use App\Application\Services\Auth\AuthService;
use App\Application\Services\Auth\AuthServiceImpl;
use App\Application\Services\Auth\TokenService;
use App\Application\Services\Auth\TokenServiceImpl;
use App\Application\Services\Company\CompanyService;
use App\Application\Services\Company\CompanyServiceImpl;
use App\Application\Services\Subscriptions\SubscriptionService;
use App\Application\Services\Subscriptions\SubscriptionServiceImpl;
use App\Application\Services\User\UserService;
use App\Application\Services\User\UserServiceImpl;
use App\Data\Repositories\CompanyRepository;
use App\Domain\Interfaces\Repositories\CompanyRepositoryInterface;
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
            //ProductService::class => ProductServiceImpl::class,

            // Settings services
            CompanyService::class => CompanyServiceImpl::class,
            UserService::class => UserServiceImpl::class,
            SubscriptionService::class => SubscriptionServiceImpl::class,

            /*
             * Repositories - These are the concrete implementations of the repository interfaces. They handle data access and manipulation.
             * 
             * By binding the repository interfaces to their concrete implementations, we can easily swap out
             * the underlying data access logic without affecting the rest of the application.
             * This promotes a clean separation of concerns and makes testing easier, as we can mock
             * the repository interfaces in our tests.
             *
             * Note: Repositories should be bound to their interfaces to allow for easier
             * testing and flexibility in implementation.
             */
            CompanyRepositoryInterface::class => CompanyRepository::class,

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
