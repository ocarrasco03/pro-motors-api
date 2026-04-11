<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Supplier\CreateSupplierDTO;
use App\Application\DTOs\Supplier\UpdateSupplierDTO;
use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierServiceInterface
{
    public function getAll(SearchDTO $searchDTO): LengthAwarePaginator;

    public function getSupplier(Supplier $supplier): Supplier;

    public function create(CreateSupplierDTO $data): Supplier;

    public function update(Supplier $supplier, UpdateSupplierDTO $data): Supplier;

    public function delete(Supplier $supplier): bool;
}
