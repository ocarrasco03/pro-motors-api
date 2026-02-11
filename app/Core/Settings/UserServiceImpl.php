<?php

namespace App\Core\Settings;

use App\Core\DTO\Common\SearchDTO;
use App\Core\Settings\UserService;
use App\Http\Resources\Settings\UserCollection;
use App\Http\Resources\Settings\UserResource;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Throwable;

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
    public function getUsers(SearchDTO $data): UserCollection
    {
        $perPage = $data->perPage ?? 10;
        $sortBy = $data->sortBy ?? 'id';
        $orderBy = $data->orderBy ?? 'asc';
        $filterBy = $data->filterBy ?? [];
        $search = $data->search ?? '';

        if ($search && method_exists(User::class, 'search')) {
            return new UserCollection(
                User::search($search, function ($engine, $query, $options) {
                    $options['filter'] = 'company_id = ' . $this->authUser()->company_id;
                    return $engine->search($query, $options);
                })->paginate($perPage)
            );
        }

        $query = User::query()
            ->visibleFor($data->authUser)
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
    public function getUser(User $user): UserResource
    {
        return new UserResource($user->refresh()->load(['roles', 'permissions', 'company']));
    }

    /**
     * Create a new user.
     * @throws Throwable
     */
    public function createUser(array $data): UserResource
    {
        try{
            DB::beginTransaction();

            $data['password'] = Hash::make($data['password']);

            if (! is_null($this->authUser()->company_id)) {
                $data['company_id'] = $this->authUser()->company_id;
                unset($data['company']);
            } elseif (array_key_exists('company', $data)) {
                $data['company_id'] = $this->resolveCompany($data['company']);
            }

            $user = User::create($data);

            $this->assignRole($user, $data['role']);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            Log::error($exception->getMessage());
            throw new Exception($exception->getMessage());
        }

        return new UserResource(
            $user
                ->refresh()
                ->load(['roles', 'company'])
        );
    }

    /**
     * Update an existing user.
     * @throws Exception|Throwable
     */
    public function updateUser(User $user, array $data): UserResource
    {
        try {
            DB::beginTransaction();

            if (isset($data['company_id']) || isset($data['company'])) {
                if (! is_null($this->authUser()->company_id)) {
                    $data['company_id'] = $this->authUser()->company_id;
                    unset($data['company']);
                } elseif (array_key_exists('company', $data)) {
                    $data['company_id'] = $this->resolveCompany($data['company']);
                }
            }

            $user->update($data);

            if (isset($data['role'])) {
                $this->assignRole($user, $data['role']);
            }

            DB::commit();
        } catch (Throwable|Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            throw new Exception($exception->getMessage());
        }

        return new UserResource($user->refresh()->load(['roles', 'company']));
    }

    /**
     * Soft delete a user.
     */
    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
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
    public function resetPassword(User $user, array $data): bool
    {
        $user->password = Hash::make($data['password']);

        return $user->save();
    }

    /**
     * Determine whether a user is active.
     */
    public function isUserActive(User $user): bool
    {
        return (bool) $user->active;
    }

    /**
     * Enable or disable a user.
     */
    public function enableDisableUser(User $user): bool
    {
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
     *
     * @throws Throwable
     */
    public function assignRole(User $user, string|Role $role): void
    {
        DB::transaction(function () use ($user, $role) {
            $user->syncRoles([$role]);
        });
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(User $user, array $data): void
    {
        $user->removeRole($data['role']);
    }

    /**
     * Get roles assigned to a user.
     */
    public function getRoles(User $user): Collection
    {
        return $user->roles;
    }

    /**
     * Assign a permission to a user.
     */
    public function assignPermission(User $user, array $data): void
    {
        $user->givePermissionTo($data['permission']);
    }

    /**
     * Remove a permission from a user.
     */
    public function removePermission(User $user, array $data): void
    {
        $user->revokePermissionTo($data['permission']);
    }

    /**
     * Get permissions assigned directly to a user.
     */
    public function getPermissions(User $user): Collection
    {
        return $user->all_permissions;
    }

    protected function resolveCompany(int|string $company): int
    {
        if (is_numeric($company)) {
            return (int) $company;
        }

        return Company::where('name', $company)->pluck('id')->firstOrFail();
    }
}
