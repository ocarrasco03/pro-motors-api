<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Product\AddEquivalenceDTO;
use App\Application\DTOs\Product\AddRelationDTO;
use App\Application\DTOs\Product\CreateProductDTO;
use App\Application\DTOs\Product\UpdateProductDTO;
use App\Domain\Interfaces\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Support\Traits\AuthUser;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductServiceImpl implements ProductServiceInterface
{
    use AuthUser;

    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {}

    public function getAll(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $filters = [
            'per_page' => $searchDTO->perPage ?? 15,
            'sort_by' => $searchDTO->sortBy ?? 'id',
            'order_by' => $searchDTO->orderBy ?? 'desc',
            'search' => $searchDTO->search ?? '',
            'brand_id' => $searchDTO->filterBy['brand_id'] ?? null,
            'active_only' => $searchDTO->filterBy['active_only'] ?? false,
        ];

        return $this->repository->paginate($filters);
    }

    public function getProduct(Product $product): Product
    {
        return $product->refresh()->load([
            'brand',
            'suppliers',
            'priceLists.company',
            'equivalences.product.brand',
            'equivalentTo.product.brand',
            'relations.product.brand',
            'relatedTo.product.brand',
        ]);
    }

    public function create(CreateProductDTO $data): Product
    {
        return $this->repository->create($data->toArray());
    }

    public function update(Product $product, UpdateProductDTO $data): Product
    {
        return $this->repository->update($product, $data->toArray());
    }

    public function delete(Product $product): bool
    {
        return $this->repository->delete($product);
    }

    public function addEquivalence(Product $product, AddEquivalenceDTO $data): Product
    {
        return $this->repository->addEquivalence($product, $data->toArray());
    }

    public function removeEquivalence(Product $product, int $equivalentProductId): bool
    {
        return $this->repository->removeEquivalence($product, $equivalentProductId);
    }

    public function addRelation(Product $product, AddRelationDTO $data): Product
    {
        return $this->repository->addRelation($product, $data->toArray());
    }

    public function removeRelation(Product $product, int $relatedProductId): bool
    {
        return $this->repository->removeRelation($product, $relatedProductId);
    }

    public function findBySkuAndBrand(string $sku, int $brandId, string $description): ?Product
    {
        return $this->repository->findBySkuAndBrand($sku, $brandId, $description);
    }
}
