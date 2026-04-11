<?php

declare(strict_types=1);

namespace App\Application\Services\Product;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\PriceList\CreatePriceListDTO;
use App\Application\DTOs\PriceList\SetProductPriceDTO;
use App\Application\DTOs\PriceList\UpdatePriceListDTO;
use App\Domain\Interfaces\Repositories\PriceListRepositoryInterface;
use App\Domain\ValueObjects\Enums\RolesEnum;
use App\Models\PriceList;
use App\Models\User;
use App\Support\Traits\AuthUser;
use Illuminate\Pagination\LengthAwarePaginator;

class PriceListServiceImpl implements PriceListServiceInterface
{
    use AuthUser;

    public function __construct(
        protected PriceListRepositoryInterface $repository
    ) {}

    public function getAllForCompany(User $user, SearchDTO $searchDTO): LengthAwarePaginator
    {
        $companyId = $this->getCompanyIdForUser($user);

        if (! $companyId) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        $filters = [
            'per_page' => $searchDTO->perPage ?? 15,
            'sort_by' => $searchDTO->sortBy ?? 'name',
            'order_by' => $searchDTO->orderBy ?? 'asc',
            'search' => $searchDTO->search ?? '',
            'active_only' => $searchDTO->filterBy['active_only'] ?? false,
        ];

        return $this->repository->paginateForCompany($companyId, $filters);
    }

    public function getPriceList(PriceList $priceList): PriceList
    {
        return $priceList->refresh()->load(['company', 'products.brand']);
    }

    public function create(CreatePriceListDTO $data): PriceList
    {
        return $this->repository->create($data->toArray());
    }

    public function update(PriceList $priceList, UpdatePriceListDTO $data): PriceList
    {
        return $this->repository->update($priceList, $data->toArray());
    }

    public function delete(PriceList $priceList): bool
    {
        return $this->repository->delete($priceList);
    }

    public function setProductPrice(PriceList $priceList, SetProductPriceDTO $data): PriceList
    {
        return $this->repository->setProductPrice($priceList, $data->toArray());
    }

    public function removeProductPrice(PriceList $priceList, int $productId): bool
    {
        return $this->repository->removeProductPrice($priceList, $productId);
    }

    private function getCompanyIdForUser(User $user): ?int
    {
        if ($user->hasRole(RolesEnum::SUPER_ADMIN)) {
            return $user->company_id;
        }

        return $user->company_id;
    }
}
