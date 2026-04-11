<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Supplier\CreateSupplierDTO;
use App\Application\DTOs\Supplier\UpdateSupplierDTO;
use App\Application\Services\Product\SupplierServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Product\SupplierRequest;
use App\Http\Requests\Product\SupplierUpdateRequest;
use App\Http\Resources\Product\SupplierCollection;
use App\Http\Resources\Product\SupplierResource;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierServiceInterface $supplierService
    ) {
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    public function index(SearchRequest $request)
    {
        $dto = SearchDTO::fromArray($request->validated());
        $suppliers = $this->supplierService->getAll($dto);

        return $this->success(new SupplierCollection($suppliers));
    }

    public function store(SupplierRequest $request)
    {
        $dto = CreateSupplierDTO::fromArray($request->validated());
        $supplier = $this->supplierService->create($dto);

        return $this->success(new SupplierResource($supplier), 'Supplier has been created.', 201);
    }

    public function show(Supplier $supplier)
    {
        $supplier = $this->supplierService->getSupplier($supplier);

        return $this->success(new SupplierResource($supplier));
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        $dto = UpdateSupplierDTO::fromArray($request->validated());
        $result = $this->supplierService->update($supplier, $dto);

        return $this->success(new SupplierResource($result), 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->supplierService->delete($supplier);

        return $this->success(null, 'Supplier has been deleted.', 204);
    }
}
