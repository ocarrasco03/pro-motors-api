<?php

namespace App\Domain\Interfaces\Repositories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    public function findById(int $id): ?Company;
    public function findBySlug(string $slug): ?Company;
    public function paginateAccessibleBy(User $user, array $filters = []): LengthAwarePaginator;
    public function create(array $data): Company;
    public function update(Company $company, array $data): Company;
    public function delete(Company $company): bool;
    public function assignToGroup(Company $company, int $groupId): Company;
}
