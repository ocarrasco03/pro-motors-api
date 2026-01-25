<?php

namespace App\Core\Settings;

use App\Http\Resources\Settings\UserCollection;
use App\Models\User;
use Illuminate\Support\Collection;

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
    public function getUsers(): UserCollection;

    /**
     * Retrieve a single user by ID.
     */
    public function getUser(int $id): User;

    /**
     * Create a new user.
     */
    public function createUser(array $data): User;

    /**
     * Update an existing user.
     */
    public function updateUser(int $id, array $data): User;

    /**
     * Soft delete a user.
     */
    public function deleteUser(int $id): bool;

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser(int $id): bool;

    /**
     * Reset a user's password.
     */
    public function resetPassword(int $id, array $data): bool;

    /**
     * Determine whether a user is active.
     */
    public function isUserActive(int $id): bool;

    /**
     * Enable or disable a user.
     */
    public function enableDisableUser(int $id): bool;

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
    public function assignRole(int $id, array $data): void;

    /**
     * Remove a role from a user.
     */
    public function removeRole(int $id, array $data): void;

    /**
     * Get roles assigned to a user.
     */
    public function getRoles(int $id): Collection;

    /**
     * Assign a permission to a user.
     */
    public function assignPermission(int $id, array $data): void;

    /**
     * Remove a permission from a user.
     */
    public function removePermission(int $id, array $data): void;

    /**
     * Get permissions assigned directly to a user.
     */
    public function getPermissions(int $id): Collection;
}
