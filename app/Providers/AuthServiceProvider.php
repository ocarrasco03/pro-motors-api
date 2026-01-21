<?php

namespace App\Providers;

use App\Core\Enums\RolesEnum;
use App\Models\Company;
use App\Models\User;
use App\Policies\Settings\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        // Future:
        // PriceList::class => PriceListPolicy::class,
        // Product::class   => ProductPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Global override: Super Admin
         *
         * This allows a super-admin role to bypass
         * all policy checks safely and explicitly.
         */
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole(RolesEnum::SUPER_ADMIN->value) ? true : null;
        });

        /**
         * Optional explicit gates (NO business logic)
         *
         * Úsalos solo para:
         * - Acciones globales
         * - Acciones no ligadas a un modelo
         */
        Gate::define('access-admin-panel', function (User $user) {
            return $user->can('admin.access');
        });
    }
}
