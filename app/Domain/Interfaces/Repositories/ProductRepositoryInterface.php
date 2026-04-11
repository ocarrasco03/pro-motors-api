<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findBySkuAndBrand(string $sku, int $brandId, string $description): ?Product;

    public function paginate(array $filters = []): LengthAwarePaginator;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;

    public function addEquivalence(Product $product, array $data): Product;

    public function removeEquivalence(Product $product, int $equivalentProductId): bool;

    public function addRelation(Product $product, array $data): Product;

    public function removeRelation(Product $product, int $relatedProductId): bool;
}
