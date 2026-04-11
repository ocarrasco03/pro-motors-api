<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\ValueObjects\Enums\PermissionsEnum;
use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionsEnum::LIST_PRODUCTS);
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->can(PermissionsEnum::VIEW_PRODUCT);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionsEnum::CREATE_PRODUCT);
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->can(PermissionsEnum::EDIT_PRODUCT);
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->can(PermissionsEnum::DELETE_PRODUCT);
    }
}
