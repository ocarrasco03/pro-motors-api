<?php

namespace App\Application\Services\User;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\User\StoreUserDTO;
use App\Application\DTOs\User\UpdateUserDTO;
use App\Application\Services\User\UserService;
use App\Domain\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\Company;
use App\Models\User;
use App\Support\Traits\AuthUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Throwable;

class UserServiceImpl implements UserService
{
    use AuthUser;

    public function __construct(
        protected UserRepositoryInterface $repository,
        protected CompanyRepositoryInterface $companyRepository
    ) {}

    public function getAll(SearchDTO $data): LengthAwarePaginator
    {
        $filters = [
            'per_page' => $data->perPage ?? 10,
            'sort_by' => $data->sortBy ?? 'id',
            'order_by' => $data->orderBy ?? 'asc',
            'search' => $data->search ?? '',
            'filter_by' => $data->filterBy ?? []
        ];

        return $this->repository->paginateAccesibleBy($this->authUser(), $filters);
    }

    public function getUser(User $user): User
    {
        return $user->refresh()->loadMissing(['roles', 'permissions', 'company']);
    }

    public function create(StoreUserDTO $data): User
    {
        return $this->repository->create($data->toArray());
    }

    public function update(User $user, UpdateUserDTO $data): User
    {
        return $this->repository->update($user, $data->toArray());
    }

    public function delete(User $user): bool
    {
        return $this->repository->delete($user);
    }

    public function restore(int $id): bool
    {
        return $this->repository->restore($id);
    }

    public function forceDelete(User $user): bool
    {
        return $this->repository->forceDelete($user);
    }

    /**
     * Reset a user's password.
     */
    public function resetPassword(User $user, array $data): bool
    {
        $user->password = Hash::make($data['password']);

        return $user->save();
    }

    public function enableDisableUser(User $user): bool
    {
        $user->active = ! $user->active;

        return $user->save();
    }

    public function getCompanyUsers(int $companyId): LengthAwarePaginator
    {
        return $this->repository->findByCompanyId($companyId);
    }

    /**
     * Get the company associated with a user.
     */
    public function getUserCompany(User $user): Company
    {
        return $this->companyRepository->findById($user->company_id);
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
    public function removeRole(User $user, string $role): void
    {
        $user->removeRole($role);
    }

    /**
     * Get roles assigned to a user.
     */
    public function getRoles(User $user): Collection
    {
        return $user->getRoleNames();
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
            if (! Company::where('id', $company)->exists()) {
                throw new ModelNotFoundException("Company with ID {$company} does not exist.");
            }

            return (int) $company;
        }

        return Company::where('name', $company)->pluck('id')->firstOrFail();
    }
}
