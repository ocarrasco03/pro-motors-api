<?php

namespace App\Providers;

use App\Data\Repositories\BrandRepository;
use App\Data\Repositories\CompanyRepository;
use App\Data\Repositories\PriceListRepository;
use App\Data\Repositories\ProductRepository;
use App\Data\Repositories\ScheduledTaskRepository;
use App\Data\Repositories\SupplierRepository;
use App\Data\Repositories\UserRepository;
use App\Domain\Interfaces\Repositories\BrandRepositoryInterface;
use App\Domain\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Domain\Interfaces\Repositories\PriceListRepositoryInterface;
use App\Domain\Interfaces\Repositories\ProductRepositoryInterface;
use App\Domain\Interfaces\Repositories\ScheduledTaskRepositoryInterface;
use App\Domain\Interfaces\Repositories\SupplierRepositoryInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $bindings = [
            BrandRepositoryInterface::class => BrandRepository::class,
            CompanyRepositoryInterface::class => CompanyRepository::class,
            PriceListRepositoryInterface::class => PriceListRepository::class,
            ProductRepositoryInterface::class => ProductRepository::class,
            ScheduledTaskRepositoryInterface::class => ScheduledTaskRepository::class,
            SupplierRepositoryInterface::class => SupplierRepository::class,
            UserRepositoryInterface::class => UserRepository::class,
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
