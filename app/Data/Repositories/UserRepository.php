<?php

namespace App\Data\Repositories;

use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\Company;
use App\Models\User;
use App\Support\Traits\AuthUser;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserRepositoryInterface
{
    use AuthUser;

    public function findById(int $id): ?User
    {
        return User::with([
            'company:id,name,slug',
            'roles:name'
        ])->find($id);
    }

    public function findByEmail(string $email): ?LengthAwarePaginator
    {
        return User::with([
                'company:id,name,slug',
                'roles:name'
            ])
            ->where('email', $email)
            ->paginate();
    }

    public function findByCompanyId(int $companyId): ?LengthAwarePaginator
    {
        return User::with([
                'company:id,name,slug',
                'roles:name'
            ])
            ->where('company_id', $companyId)
            ->paginate();
    }

    public function findByUsername(string $username): ?User
    {
        return User::with([
                'company:id,name,slug',
                'roles:name'
            ])
            ->where('username', $username)
            ->first();
    }

    public function paginateAccesibleBy(User $user, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 10;
        $sortBy = $filters['sort_by'] ?? 'id';
        $orderBy = $filters['order_by'] ?? 'asc';
        $search = $filters['search'] ?? '';
        $filterBy = $filters['filter_by'] ?? [];

        if ($search && method_exists(User::class, 'search')) {
            return User::search($search, function ($engine, $query, $options) use ($user) {
                    $options['filter'] = 'company_id = ' . $user->company_id;
                    return $engine->search($query, $options);
                })
                ->with(['company:id,name,slug', 'roles:name'])
                ->paginate($perPage);
        }

        $query = User::query()
            ->visibleFor($user)
            ->with(['company:id,name,slug', 'roles:name'])
            ->orderBy($sortBy, $orderBy);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filterBy)) {
            foreach ($filterBy as $field => $value) {
                $query->where($field, $value);
            }
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): User
    {
        try {
            DB::beginTransaction();

            $data['password'] = Hash::make($data['password']);

            if (!is_null($this->authUser()->company_id)) {
                $data['company_id'] = $this->authUser()->company_id;
                unset($data['company_name']);
            } elseif (isset($data['company_name'])) {
                $data['company_id'] = $this->resolveCompany($data['company_name']);
            }

            $user = User::create($data);

            $this->assignRole($user, $data['role']);

            DB::commit();

            Log::info("User created successfully: ID {$user->id}, Username: {$user->username}");

            return $user->load(['company:id,name,slug', 'roles:name']);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error("Error creating user: " . $e->getMessage());
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

    public function update(User $user, array $data): User
    {
        try {
            DB::beginTransaction();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if (!is_null($this->authUser()->company_id)) {
                $data['company_id'] = $this->authUser()->company_id;
                unset($data['company_name']);
            } elseif (isset($data['company_name'])) {
                $data['company_id'] = $this->resolveCompany($data['company_name']);
            }

            $user->update($data);

            DB::commit();

            Log::info("User created successfully: ID {$user->id}, Username: {$user->username}");

            return $user->load(['company:id,name,slug', 'roles:name']);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error("Error creating user: " . $e->getMessage());
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function restore(int $id): bool
    {
        $user = User::withTrashed()->find($id);

        return (bool) $user->restore();
    }

    public function forceDelete(User $user): bool
    {
        return (bool) $user->forceDelete();
    }

    public function assignCompany(User $user, int $companyId): User
    {
        $user->update(['company_id' => $companyId]);

        return $user->fresh(['company:id,name,slug', 'roles:name']);
    }

    public function assignRole(User $user, string $role): User
    {
        $user->syncRoles($role);

        return $user->fresh(['company:id,name,slug', 'roles:name']);
    }

    private function resolveCompany(string $companyName): int
    {
        $company = Company::where('name', $companyName)->first();

        if (! $company) {
            Log::warning("Company with name '{$companyName}' not found.");
            throw new Exception("Company with name '{$companyName}' not found.");
        }

        return $company->id;
    }
}
