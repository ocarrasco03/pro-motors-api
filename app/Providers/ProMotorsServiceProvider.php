<?php

namespace App\Providers;

use App\Application\Services\Auth\AuthService;
use App\Application\Services\Auth\AuthServiceImpl;
use App\Application\Services\Auth\TokenService;
use App\Application\Services\Auth\TokenServiceImpl;
use App\Application\Services\Company\CompanyService;
use App\Application\Services\Company\CompanyServiceImpl;
use App\Application\Services\Product\BrandServiceImpl;
use App\Application\Services\Product\BrandServiceInterface;
use App\Application\Services\Product\PriceListServiceImpl;
use App\Application\Services\Product\PriceListServiceInterface;
use App\Application\Services\Product\ProductServiceImpl;
use App\Application\Services\Product\ProductServiceInterface;
use App\Application\Services\Product\SupplierServiceImpl;
use App\Application\Services\Product\SupplierServiceInterface;
use App\Application\Services\ScheduledTask\ScheduledTaskService;
use App\Application\Services\ScheduledTask\ScheduledTaskServiceImpl;
use App\Application\Services\Subscriptions\SubscriptionService;
use App\Application\Services\Subscriptions\SubscriptionServiceImpl;
use App\Application\Services\User\UserService;
use App\Application\Services\User\UserServiceImpl;
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
            AuthService::class => AuthServiceImpl::class,

            // Catalogs services
            BrandServiceInterface::class => BrandServiceImpl::class,
            SupplierServiceInterface::class => SupplierServiceImpl::class,
            ProductServiceInterface::class => ProductServiceImpl::class,
            PriceListServiceInterface::class => PriceListServiceImpl::class,

            // Settings services
            CompanyService::class => CompanyServiceImpl::class,
            UserService::class => UserServiceImpl::class,
            SubscriptionService::class => SubscriptionServiceImpl::class,

            // Scheduler services
            ScheduledTaskService::class => ScheduledTaskServiceImpl::class,
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
