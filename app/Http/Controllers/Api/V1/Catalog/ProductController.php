<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Product\AddEquivalenceDTO;
use App\Application\DTOs\Product\AddRelationDTO;
use App\Application\DTOs\Product\CreateProductDTO;
use App\Application\DTOs\Product\UpdateProductDTO;
use App\Application\Services\Product\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Product\EquivalenceRequest;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\Product\RelationRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductServiceInterface $productService
    ) {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(SearchRequest $request)
    {
        $dto = SearchDTO::fromArray($request->validated());
        $products = $this->productService->getAll($dto);

        return $this->success(new ProductCollection($products));
    }

    public function store(ProductRequest $request)
    {
        $dto = CreateProductDTO::fromArray($request->validated());
        $product = $this->productService->create($dto);

        return $this->success(new ProductResource($product), 'Product has been created.', 201);
    }

    public function show(Product $product)
    {
        $product = $this->productService->getProduct($product);

        return $this->success(new ProductResource($product));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $dto = UpdateProductDTO::fromArray($request->validated());
        $result = $this->productService->update($product, $dto);

        return $this->success(new ProductResource($result), 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return $this->success(null, 'Product has been deleted.', 204);
    }

    public function addEquivalence(EquivalenceRequest $request, Product $product): JsonResponse
    {
        $dto = AddEquivalenceDTO::fromArray($request->validated());
        $product = $this->productService->addEquivalence($product, $dto);

        return $this->success(new ProductResource($product), 'Equivalence added successfully.');
    }

    public function removeEquivalence(Product $product, int $equivalentId): JsonResponse
    {
        $this->productService->removeEquivalence($product, $equivalentId);

        return $this->success(null, 'Equivalence removed successfully.');
    }

    public function addRelation(RelationRequest $request, Product $product): JsonResponse
    {
        $dto = AddRelationDTO::fromArray($request->validated());
        $product = $this->productService->addRelation($product, $dto);

        return $this->success(new ProductResource($product), 'Relation added successfully.');
    }

    public function removeRelation(Product $product, int $relatedId): JsonResponse
    {
        $this->productService->removeRelation($product, $relatedId);

        return $this->success(null, 'Relation removed successfully.');
    }
}
