<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Repositories;

use App\Models\PriceList;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PriceListRepositoryInterface
{
    public function findById(int $id): ?PriceList;

    public function findBySlug(string $slug): ?PriceList;

    public function paginateForCompany(int $companyId, array $filters = []): LengthAwarePaginator;

    public function create(array $data): PriceList;

    public function update(PriceList $priceList, array $data): PriceList;

    public function delete(PriceList $priceList): bool;

    public function setProductPrice(PriceList $priceList, array $data): PriceList;

    public function removeProductPrice(PriceList $priceList, int $productId): bool;

    public function getDefaultForCompany(int $companyId): ?PriceList;
}
