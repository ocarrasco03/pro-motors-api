<?php

namespace App\Policies\Settings;

use App\Core\Enums\PermissionsEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionsEnum::MANAGE_COMPANIES->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        return $user->can(PermissionsEnum::VIEW_COMPANY->value, $company)
            && $company->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionsEnum::CREATE_COMPANY->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->can(PermissionsEnum::EDIT_COMPANY->value, $company)
            && $company->isEditableBy($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        return $user->can(PermissionsEnum::DELETE_COMPANY->value, $company)
            && ! $company->is_protected;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return $user->can(PermissionsEnum::RESTORE_COMPANY->value, $company);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_COMPANY->value, $company);
    }
}
