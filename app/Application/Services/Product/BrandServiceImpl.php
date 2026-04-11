<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Brand\CreateBrandDTO;
use App\Application\DTOs\Brand\UpdateBrandDTO;
use App\Application\DTOs\Common\SearchDTO;
use App\Domain\Interfaces\Repositories\BrandRepositoryInterface;
use App\Models\Brand;
use App\Support\Traits\AuthUser;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandServiceImpl implements BrandServiceInterface
{
    use AuthUser;

    public function __construct(
        protected BrandRepositoryInterface $repository
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

    public function getBrand(Brand $brand): Brand
    {
        return $brand->refresh();
    }

    public function create(CreateBrandDTO $data): Brand
    {
        return $this->repository->create($data->toArray());
    }

    public function update(Brand $brand, UpdateBrandDTO $data): Brand
    {
        return $this->repository->update($brand, $data->toArray());
    }

    public function delete(Brand $brand): bool
    {
        return $this->repository->delete($brand);
    }
}
