<?php

namespace App\Http\Controllers\Settings;

use App\Core\Settings\CompanyService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Settings\CompanyRequest;
use App\Http\Requests\Settings\CompanyUpdateRequest;
use App\Models\Company;

class CompanyController extends Controller
{
    public function __construct(protected CompanyService $companyService) {
        $this->authorizeResource(Company::class, 'company');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request)
    {
        return $this->success($this->companyService->getCompanies($request->toDTO()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        return $this->success($this->companyService->createCompany($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return $this->success($this->companyService->getCompany($company));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyUpdateRequest $request, string $id)
    {
        $updated = $this->companyService->updateCompany($id, $request->validated());

        if ($updated) {
            return $this->success("Company has been updated");
        } else {
            return $this->error("Failed to update company");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int|string $company)
    {
        $deleted = $this->companyService->deleteCompany($company);

        if ($deleted) {
            return $this->success(null, "Company has been deleted");
        } else {
            return $this->error("Failed to delete company");
        }
    }
}
