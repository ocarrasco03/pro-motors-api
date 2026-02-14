<?php

namespace App\Application\Services\User;

use App\Application\DTOs\Common\SearchDTO;
use App\Http\Resources\Settings\UserCollection;
use App\Http\Resources\Settings\UserResource;
use App\Models\User;
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
    /**
     * Retrieve all users.
     */
    public function getUsers(SearchDTO $data): UserCollection;

    /**
     * Retrieve a single user by ID.
     */
    public function getUser(User $user): UserResource;

    /**
     * Create a new user.
     */
    public function createUser(array $data): UserResource;

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): UserResource;

    /**
     * Soft delete a user.
     */
    public function deleteUser(User $user): bool;

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser(int $id): bool;

    /**
     * Reset a user's password.
     */
    public function resetPassword(User $user, array $data): bool;

    /**
     * Determine whether a user is active.
     */
    public function isUserActive(User $user): bool;

    /**
     * Enable or disable a user.
     */
    public function enableDisableUser(User $user): bool;

    /**
     * Get all users belonging to a company.
     *
     * @return Collection<User>
     */
    public function getCompanyUsers(int $companyId): Collection;

    /**
     * Get the company associated with a user.
     */
    public function getUserCompany(User $user): array;

    /**
     * Get all price lists assigned to a company.
     */
    public function getAssignedPriceLists(int $companyId): array;

    /**
     * Assign a role to a user.
     */
    public function assignRole(User $user, string|Role $role): void;

    /**
     * Remove a role from a user.
     */
    public function removeRole(User $user, array $data): void;

    /**
     * Get roles assigned to a user.
     */
    public function getRoles(User $user): Collection;

    /**
     * Assign a permission to a user.
     */
    public function assignPermission(User $user, array $data): void;

    /**
     * Remove a permission from a user.
     */
    public function removePermission(User $user, array $data): void;

    /**
     * Get permissions assigned directly to a user.
     */
    public function getPermissions(User $user): Collection;
}
