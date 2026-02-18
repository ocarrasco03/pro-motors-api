<?php

namespace App\Domain\Interfaces\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?LengthAwarePaginator;
    public function findByCompanyId(int $companyId): ?LengthAwarePaginator;
    public function findByUsername(string $username): ?User;
    public function paginateAccesibleBy(User $user, array $filters = []): LengthAwarePaginator;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function delete(User $user): bool;
    public function restore(int $id): bool;
    public function forceDelete(User $user): bool;
    public function assignCompany(User $user, int $companyId): User;
    public function assignRole(User $user, string $role): User;
}
