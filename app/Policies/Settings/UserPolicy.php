<?php

namespace App\Policies\Settings;

use App\Core\Enums\PermissionsEnum;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionsEnum::LIST_USERS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can(PermissionsEnum::VIEW_USER) &&
            $user->canSeeUser($model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionsEnum::CREATE_USER);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return ($user->can(PermissionsEnum::EDIT_USER) &&
            $user->isEditableFor($model)) || $user->id === $model->id;
    }

    /**
     * Determine whether the user can change the password of the model
     */
    public function changePassword(User $user, User $model): bool
    {
        return $user->can(PermissionsEnum::CHANGE_USER_PASSWORD);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can(PermissionsEnum::DELETE_USER) &&
            $user->isDestroyableFor($model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can(PermissionsEnum::RESTORE_USER->value);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_USER->value);
    }
}
