<?php

namespace App\Providers;

use App\Application\Services\Scheduler\SchedulerBootService;
use App\Application\Services\Scheduler\SchedulerBootServiceImpl;
use App\Infrastructure\Search\Meilisearch\Configurators\UserIndexConfigurator;
use App\Infrastructure\Search\Meilisearch\Support\MeilisearchConfigurator;
use App\Models\Company;
use App\Models\User;
use App\Observers\CompanyObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SchedulerBootService::class, fn () => new SchedulerBootServiceImpl);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MeilisearchConfigurator $configurator): void
    {
        Company::observe(CompanyObserver::class);
        User::observe(UserObserver::class);

        if (Schema::hasTable('scheduled_tasks') && Schema::hasColumn('scheduled_tasks', 'frequency')) {
            $this->app->make(SchedulerBootService::class)->registerDefaultTasks();
        }

        if (config('scout.driver') !== 'meilisearch') {
            return;
        }

        $configurator->configure(new UserIndexConfigurator);
    }
}
