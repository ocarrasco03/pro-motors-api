<?php

namespace App\Application\Services\Company;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Company\CreateCompanyDTO;
use App\Application\DTOs\Company\UpdateCompanyDTO;
use App\Application\Services\Company\CompanyService;
use App\Domain\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Models\Company;
use App\Support\Traits\AuthUser;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyServiceImpl implements CompanyService
{
    use AuthUser;

    public function __construct(
        protected CompanyRepositoryInterface $repository
    ) {}

    public function getAll(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $filters = [
            'per_page' => $searchDTO->perPage ?? 10,
            'sort_by' => $searchDTO->sortBy ?? 'id',
            'order_by' => $searchDTO->orderBy ?? 'asc',
            'search' => $searchDTO->search ?? '',
            'filter_by' => $searchDTO->filterBy ?? []
        ];

        return $this->repository->paginateAccessibleBy($this->authUser(), $filters);
    }

    public function getCompany(Company $company): Company
    {
        return $company->refresh()->loadMissing(['companyGroup', 'tax']);
    }

    public function create(CreateCompanyDTO $data): Company
    {
        return $this->repository->create($data->toArray());
    }

    public function update(Company $company, UpdateCompanyDTO $data): Company
    {
        return $this->repository->update($company, $data->toArray());
    }

    public function delete(Company $company): bool
    {
        return $this->repository->delete($company);
    }
}
