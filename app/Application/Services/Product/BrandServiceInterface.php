<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Brand\CreateBrandDTO;
use App\Application\DTOs\Brand\UpdateBrandDTO;
use App\Application\DTOs\Common\SearchDTO;
use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandServiceInterface
{
    public function getAll(SearchDTO $searchDTO): LengthAwarePaginator;

    public function getBrand(Brand $brand): Brand;

    public function create(CreateBrandDTO $data): Brand;

    public function update(Brand $brand, UpdateBrandDTO $data): Brand;

    public function delete(Brand $brand): bool;
}
