<?php

namespace App\Providers;

use App\Data\Repositories\CompanyRepository;
use App\Data\Repositories\ScheduledTaskRepository;
use App\Data\Repositories\UserRepository;
use App\Domain\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Domain\Interfaces\Repositories\ScheduledTaskRepositoryInterface;
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
            CompanyRepositoryInterface::class => CompanyRepository::class,
            ScheduledTaskRepositoryInterface::class => ScheduledTaskRepository::class,
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
