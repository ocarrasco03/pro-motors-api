<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Application\DTOs\Brand\CreateBrandDTO;
use App\Application\DTOs\Brand\UpdateBrandDTO;
use App\Application\DTOs\Common\SearchDTO;
use App\Application\Services\Product\BrandServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Product\BrandRequest;
use App\Http\Requests\Product\BrandUpdateRequest;
use App\Http\Resources\Product\BrandCollection;
use App\Http\Resources\Product\BrandResource;
use App\Models\Brand;

class BrandController extends Controller
{
    public function __construct(
        protected BrandServiceInterface $brandService
    ) {
        $this->authorizeResource(Brand::class, 'brand');
    }

    public function index(SearchRequest $request)
    {
        $dto = SearchDTO::fromArray($request->validated());
        $brands = $this->brandService->getAll($dto);

        return $this->success(new BrandCollection($brands));
    }

    public function store(BrandRequest $request)
    {
        $dto = CreateBrandDTO::fromArray($request->validated());
        $brand = $this->brandService->create($dto);

        return $this->success(new BrandResource($brand), 'Brand has been created.', 201);
    }

    public function show(Brand $brand)
    {
        $brand = $this->brandService->getBrand($brand);

        return $this->success(new BrandResource($brand));
    }

    public function update(BrandUpdateRequest $request, Brand $brand)
    {
        $dto = UpdateBrandDTO::fromArray($request->validated());
        $result = $this->brandService->update($brand, $dto);

        return $this->success(new BrandResource($result), 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        $this->brandService->delete($brand);

        return $this->success(null, 'Brand has been deleted.', 204);
    }
}
