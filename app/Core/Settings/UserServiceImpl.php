<?php

namespace App\Core\Settings;

use App\Http\Resources\Settings\UserCollection;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserServiceImpl implements UserService
{
    protected ?User $authUser = null;

    protected function authUser(): User
    {
        if ($this->authUser === null) {
            $this->authUser = auth()->user();

            if (! $this->authUser) {
                throw new \RuntimeException('Authenticated user is required.');
            }
        }

        return $this->authUser;
    }

    /**
     * Retrieve all users.
     */
    public function getUsers(array $data): UserCollection
    {
        $perPage = $data['perPage'] ?? 10;
        $sortBy = $data['sortBy'] ?? 'id';
        $orderBy = $data['orderBy'] ?? 'id';
        $filterBy = $data['filterBy'];
        $page = $data['page'] ?? 1;
        $search = $data['search'] ?? null;

        if ($search && method_exists(User::class, 'search')) {
            return new UserCollection(
                User::search($search)
                    ->where('company_id', $this->authUser()->company_id)
                    ->paginate($perPage)
            );
        }

        $query = User::query()
            ->visibleFor($this->authUser())
            ->with(['company:id,name,slug', 'roles:name'])
            ->orderBy($sortBy, $orderBy);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        return new UserCollection(
            $query->paginate($perPage)
        );
    }

    /**
     * Retrieve a single user by ID.
     */
    public function getUser(int $id): User
    {
        return User::with(['roles', 'permissions', 'company'])->findOrFail($id);
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * Update an existing user.
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUser($id);
        $user->update($data);

        return $user;
    }

    /**
     * Soft delete a user.
     */
    public function deleteUser(int $id): bool
    {
        return (bool) User::findOrFail($id)->delete();
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser(int $id): bool
    {
        $user = User::withTrashed()->findOrFail($id);

        return (bool) $user->restore();
    }

    /**
     * Reset a user's password.
     */
    public function resetPassword(int $id, array $data): bool
    {
        $user = $this->getUser($id);
        $user->password = Hash::make($data['password']);

        return $user->save();
    }

    /**
     * Determine whether a user is active.
     */
    public function isUserActive(int $id): bool
    {
        $user = $this->getUser($id);

        return (bool) $user->active;
    }

    /**
     * Enable or disable a user.
     */
    public function enableDisableUser(int $id): bool
    {
        $user = $this->getUser($id);
        $user->active = ! $user->active;

        return $user->save();
    }

    /**
     * Get all users belonging to a company.
     *
     * @return Collection<User>
     */
    public function getCompanyUsers(int $companyId): Collection
    {
        return User::where('company_id', $companyId)->get();
    }

    /**
     * Get the company associated with a user.
     */
    public function getUserCompany(User $user): array
    {
        return $user->company ? $user->company->toArray() : [];
    }

    /**
     * Get all price lists assigned to a company.
     */
    public function getAssignedPriceLists(int $companyId): array
    {
        $company = Company::with('priceLists')->findOrFail($companyId);

        return $company->priceLists->toArray();
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(int $id, array $data): void
    {
        $user = $this->getUser($id);
        $user->assignRole($data['role']);
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(int $id, array $data): void
    {
        $user = $this->getUser($id);
        $user->removeRole($data['role']);
    }

    /**
     * Get roles assigned to a user.
     */
    public function getRoles(int $id): Collection
    {
        return $this->getUser($id)->roles;
    }

    /**
     * Assign a permission to a user.
     */
    public function assignPermission(int $id, array $data): void
    {
        $user = $this->getUser($id);
        $user->givePermissionTo($data['permission']);
    }

    /**
     * Remove a permission from a user.
     */
    public function removePermission(int $id, array $data): void
    {
        $user = $this->getUser($id);
        $user->revokePermissionTo($data['permission']);
    }

    /**
     * Get permissions assigned directly to a user.
     */
    public function getPermissions(int $id): Collection
    {
        return $this->getUser($id)->permissions;
    }
}
