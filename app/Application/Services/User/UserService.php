<?php

namespace App\Application\Services\User;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\User\StoreUserDTO;
use App\Application\DTOs\User\UpdateUserDTO;
use App\Models\Company;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

/**
 * Interface UserService
 *
 * Contract for managing users, roles, permissions, and company-level access rules.
 * This service is intended to encapsulate all business logic related to users,
 * enforcing enterprise-grade rules such as RBAC, soft-deletes, and company scoping.
 */
interface UserService
{
    public function getAll(SearchDTO $filters): LengthAwarePaginator;
    public function getUser(User $user): User;
    public function create(StoreUserDTO $data): User;
    public function update(User $user, UpdateUserDTO $data): User;
    public function delete(User $user): bool;
    public function restore(int $id): bool;
    public function forceDelete(User $user): bool;
    //public function enableDisableUser(User $user): bool;
    public function getCompanyUsers(int $companyId): LengthAwarePaginator;
    public function getUserCompany(User $user): Company;
    public function getAssignedPriceLists(int $companyId): array;
    public function assignRole(User $user, string|Role $role): void;
    public function getRoles(User $user): Collection;
    public function assignPermission(User $user, array $data): void;
    public function removePermission(User $user, array $data): void;
    public function getPermissions(User $user): Collection;
}
