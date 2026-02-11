<?php

namespace App\Providers;

use App\Core\Scout\Configurators\UserIndexConfigurator;
use App\Core\Scout\Support\MeilisearchConfigurator;
use App\Models\Company;
use App\Models\User;
use App\Observers\CompanyObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MeilisearchConfigurator $configurator): void
    {
        Company::observe(CompanyObserver::class);
        User::observe(UserObserver::class);

        if (config('scout.driver') !== 'meilisearch') {
            return;
        }

        $configurator->configure(new UserIndexConfigurator());
    }
}
