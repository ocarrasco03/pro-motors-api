<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Supplier\CreateSupplierDTO;
use App\Application\DTOs\Supplier\UpdateSupplierDTO;
use App\Domain\Interfaces\Repositories\SupplierRepositoryInterface;
use App\Models\Supplier;
use App\Support\Traits\AuthUser;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierServiceImpl implements SupplierServiceInterface
{
    use AuthUser;

    public function __construct(
        protected SupplierRepositoryInterface $repository
    ) {}

    public function getAll(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $filters = [
            'per_page' => $searchDTO->perPage ?? 15,
            'sort_by' => $searchDTO->sortBy ?? 'name',
            'order_by' => $searchDTO->orderBy ?? 'asc',
            'search' => $searchDTO->search ?? '',
            'active_only' => $searchDTO->filterBy['active_only'] ?? false,
        ];

        return $this->repository->paginate($filters);
    }

    public function getSupplier(Supplier $supplier): Supplier
    {
        return $supplier->refresh();
    }

    public function create(CreateSupplierDTO $data): Supplier
    {
        return $this->repository->create($data->toArray());
    }

    public function update(Supplier $supplier, UpdateSupplierDTO $data): Supplier
    {
        return $this->repository->update($supplier, $data->toArray());
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->repository->delete($supplier);
    }
}
