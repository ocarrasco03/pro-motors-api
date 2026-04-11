<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Repositories;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier;

    public function findBySlug(string $slug): ?Supplier;

    public function paginate(array $filters = []): LengthAwarePaginator;

    public function create(array $data): Supplier;

    public function update(Supplier $supplier, array $data): Supplier;

    public function delete(Supplier $supplier): bool;
}
