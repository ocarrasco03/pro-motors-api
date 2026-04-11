<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\ValueObjects\Enums\PermissionsEnum;
use App\Domain\ValueObjects\Enums\RolesEnum;
use App\Models\PriceList;
use App\Models\User;

class PriceListPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionsEnum::MANAGE_PRICE_LISTS);
    }

    public function view(User $user, PriceList $priceList): bool
    {
        if (! $user->company) {
            return false;
        }

        if ($user->hasRole(RolesEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($priceList->company_id !== $user->company_id) {
            if ($user->company->company_group_id && $priceList->company->company_group_id === $user->company->company_group_id) {
                return true;
            }

            return false;
        }

        return $user->can(PermissionsEnum::MANAGE_PRICE_LISTS);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionsEnum::MANAGE_PRICE_LISTS);
    }

    public function update(User $user, PriceList $priceList): bool
    {
        if (! $user->company) {
            return false;
        }

        if ($user->hasRole(RolesEnum::SUPER_ADMIN)) {
            return true;
        }

        return $priceList->company_id === $user->company_id && $user->can(PermissionsEnum::MANAGE_PRICE_LISTS);
    }

    public function delete(User $user, PriceList $priceList): bool
    {
        if (! $user->company) {
            return false;
        }

        if ($user->hasRole(RolesEnum::SUPER_ADMIN)) {
            return true;
        }

        return $priceList->company_id === $user->company_id && $user->can(PermissionsEnum::MANAGE_PRICE_LISTS);
    }

    public function managePrices(User $user, PriceList $priceList): bool
    {
        if (! $user->company) {
            return false;
        }

        if ($user->hasRole(RolesEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($priceList->company_id !== $user->company_id) {
            if ($user->company->company_group_id && $priceList->company->company_group_id === $user->company->company_group_id) {
                return true;
            }

            return false;
        }

        return $user->can(PermissionsEnum::MANAGE_PRICE_LISTS);
    }
}
