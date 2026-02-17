<?php

namespace App\Domain\Interfaces\Services;

use App\Application\DTOs\Company\CreateCompanyDTO;
use App\Application\DTOs\Company\UpdateCompanyDTO;
use App\Models\Company;

interface CompanyServiceInterface
{
    public function getAll(array $filters = [], ?int $perPage = null);
    public function getById(int $id): Company;
    public function create(CreateCompanyDTO $data): Company;
    public function update(int $id, UpdateCompanyDTO $data): Company;
    public function delete(int $id): bool;
    public function assignToGroup(int $companyId, int $groupId): Company;
}
