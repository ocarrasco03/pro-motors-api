<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Product\AddEquivalenceDTO;
use App\Application\DTOs\Product\AddRelationDTO;
use App\Application\DTOs\Product\CreateProductDTO;
use App\Application\DTOs\Product\UpdateProductDTO;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function getAll(SearchDTO $searchDTO): LengthAwarePaginator;

    public function getProduct(Product $product): Product;

    public function create(CreateProductDTO $data): Product;

    public function update(Product $product, UpdateProductDTO $data): Product;

    public function delete(Product $product): bool;

    public function addEquivalence(Product $product, AddEquivalenceDTO $data): Product;

    public function removeEquivalence(Product $product, int $equivalentProductId): bool;

    public function addRelation(Product $product, AddRelationDTO $data): Product;

    public function removeRelation(Product $product, int $relatedProductId): bool;

    public function findBySkuAndBrand(string $sku, int $brandId, string $description): ?Product;
}
