<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\PriceList\CreatePriceListDTO;
use App\Application\DTOs\PriceList\SetProductPriceDTO;
use App\Application\DTOs\PriceList\UpdatePriceListDTO;
use App\Models\PriceList;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface PriceListServiceInterface
{
    public function getAllForCompany(User $user, SearchDTO $searchDTO): LengthAwarePaginator;

    public function getPriceList(PriceList $priceList): PriceList;

    public function create(CreatePriceListDTO $data): PriceList;

    public function update(PriceList $priceList, UpdatePriceListDTO $data): PriceList;

    public function delete(PriceList $priceList): bool;

    public function setProductPrice(PriceList $priceList, SetProductPriceDTO $data): PriceList;

    public function removeProductPrice(PriceList $priceList, int $productId): bool;
}
