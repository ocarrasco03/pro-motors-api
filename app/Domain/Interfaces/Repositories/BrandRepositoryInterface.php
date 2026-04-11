<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Repositories;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    public function findById(int $id): ?Brand;

    public function findBySlug(string $slug): ?Brand;

    public function paginate(array $filters = []): LengthAwarePaginator;

    public function create(array $data): Brand;

    public function update(Brand $brand, array $data): Brand;

    public function delete(Brand $brand): bool;
}
