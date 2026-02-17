<?php

namespace App\Application\Services\Company;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Company\CreateCompanyDTO;
use App\Application\DTOs\Company\UpdateCompanyDTO;
use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyService
{
    public function getAll(SearchDTO $filters): LengthAwarePaginator;
    public function getCompany(Company $company): Company;
    public function create(CreateCompanyDTO $data): Company;
    public function update(Company $company, UpdateCompanyDTO $data): Company;
    public function delete(Company $company): bool;
}
