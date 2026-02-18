<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\Company\CreateCompanyDTO;
use App\Application\DTOs\Company\UpdateCompanyDTO;
use App\Application\Services\Company\CompanyService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Settings\CompanyRequest;
use App\Http\Requests\Settings\CompanyUpdateRequest;
use App\Http\Resources\Company\CompanyCollection;
use App\Http\Resources\Company\CompanyResource;
use App\Models\Company;

class CompanyController extends Controller
{
    protected $dto;

    public function __construct(protected CompanyService $companyService) {
        $this->authorizeResource(Company::class, 'company');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request)
    {
        $this->dto = SearchDTO::fromArray($request->validated());
        $companies = $this->companyService->getAll($this->dto);

        return $this->success(new CompanyCollection($companies));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $this->dto = CreateCompanyDTO::fromArray($request->validated());
        $company = $this->companyService->create($this->dto);

        return $this->success(new CompanyResource($company), 'Company has been created.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company = $this->companyService->getCompany($company);

        abort_if(!$company, 404, 'Company not found.');

        return $this->success(new CompanyResource($company));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyUpdateRequest $request, Company $company)
    {
        $this->dto = UpdateCompanyDTO::fromArray($request->validated());
        $result = $this->companyService->update($company, $this->dto);

        return $this->success(new CompanyResource($result), 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $this->companyService->delete($company);

        return $this->success(null, "Company has been deleted", 204);
    }
}
