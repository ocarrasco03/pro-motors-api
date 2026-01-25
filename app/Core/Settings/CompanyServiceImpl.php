<?php

namespace App\Core\Settings;

use App\Core\Enums\StatusEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CompanyServiceImpl implements CompanyService
{
    /**
     * Retrieves the list of all companies.
     *
     * @return array List of companies. Each element represents a company
     *               with its basic information.
     */
    public function getCompanies(): array
    {
        try {
            return Company::select(
                'id', 'name', 'email', 'slug', 'owner_name', 'phone', 'company_group_id',
                'license', 'status', 'billing_period', 'created_by', 'updated_by', 'updated_at')
                ->with(['companyGroup' => function ($query) {
                    $query->select('name');
                }])
                ->get()
                ->toArray();
            // ->paginate(20);
        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('No companies available');
        }
    }

    /**
     * Retrieves detailed information for a specific company.
     *
     * @param  int|string  $company  Unique identifier or slug of the company.
     * @return array Detailed company information.
     *
     * @throws \InvalidArgumentException If the provided ID is not valid.
     */
    public function getCompany(int|string $company): array
    {
        try {
            $query = Company::select($this->selectFields())
                ->with(['companyGroup', 'tax']);

            if (is_numeric($company)) {
                return $query
                    ->findOrFail((int) $company)
                    ->toArray();
            }

            return Company::findBySlug(
                $company,
                $this->selectFields(),
                fn ($query) => $query->with('companyGroup', 'tax'))
                ->toArray();

        } catch (ModelNotFoundException $exception) {
            throw new ModelNotFoundException('Company not found');
        }
    }

    /**
     * Creates a new company.
     *
     * During creation, the company must be associated with a default tax configuration.
     * The default tax can be modified later according to business rules.
     *
     * @param  array  $data  Company creation data (name, status, group_id, default_tax_id, etc.).
     * @return array Created company data.
     *
     * @throws \InvalidArgumentException|\Throwable If the provided data is invalid.
     */
    public function createCompany(array $data): array
    {
        try {
            DB::beginTransaction();
            $company = new Company;
            $company->fill($data);
            $company->save();

            // Currency Configuration

            DB::commit();

            return $company->toArray();
        } catch (\InvalidArgumentException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        } finally {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        }
    }

    /**
     * Updates an existing company's information.
     *
     * @param  int  $id  Unique identifier of the company.
     * @param  array  $data  Data to be updated (allowed fields according to business rules).
     * @return bool True if the update was successful, false otherwise.
     *
     * @throws \InvalidArgumentException If the provided data is invalid.
     */
    public function updateCompany(int $id, array $data): bool
    {
        // TODO: Implement updateCompany() method.
    }

    /**
     * Deletes a company.
     *
     * Deletion can be logical or physical depending on the concrete
     * service implementation.
     *
     * @param  int|string  $company  Unique identifier of the company.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteCompany(int|string $company): bool
    {
        if (is_numeric($company)) {
            return Company::destroy($company);
        }

        return Company::getBySlug($company)
            ->firstOrFail()
            ->deleteOrFail();
    }

    /**
     * Assigns a company to a group.
     *
     * When a company belongs to a group, it can access price lists
     * from other companies within the same group.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @param  int  $groupId  Unique identifier of the group.
     * @return bool True if the assignment was successful, false otherwise.
     */
    public function assignToGroup(int $companyId, int $groupId): bool
    {
        // TODO: Implement assignToGroup() method.
    }

    /**
     * Removes a company from its current group.
     *
     * After removal, the company will no longer have access
     * to price lists from other companies in the group.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @return bool True if the operation was successful, false otherwise.
     */
    public function removeFromGroup(int $companyId): bool
    {
        // TODO: Implement removeFromGroup() method.
    }

    /**
     * Determines whether a company is active.
     *
     * Inactive or disabled companies must not allow their users to authenticate.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @return bool True if the company is active, false otherwise.
     */
    public function isCompanyActive(int $companyId): bool
    {
        $company = Company::find($companyId)->pluck('status');

        return $company->status === StatusEnum::ACTIVE;
    }

    /**
     * Enables a company.
     *
     * Once enabled, users belonging to the company
     * are allowed to authenticate again.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @return bool True if the company was successfully enabled.
     */
    public function enableCompany(int $companyId): bool
    {
        return Company::where('id', $companyId)->update(['status' => StatusEnum::ACTIVE]);
    }

    /**
     * Disables a company.
     *
     * When disabled, all users belonging to the company
     * must be prevented from authenticating.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @return bool True if the company was successfully disabled.
     */
    public function disableCompany(int $companyId): bool
    {
        // TODO: Implement disableCompany() method.
    }

    /**
     * Updates the default tax configuration for a company.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @param  int  $taxId  Unique identifier of the tax to be set as default.
     * @return bool True if the default tax was successfully updated.
     */
    public function updateDefaultTax(int $companyId, int $taxId): bool
    {
        // TODO: Implement updateDefaultTax() method.
    }

    /**
     * Retrieves the default tax configuration for a company.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @return array Default tax information.
     */
    public function getDefaultTax(int $companyId): array
    {
        // TODO: Implement getDefaultTax() method.
    }

    /**
     * Retrieves all price lists accessible by a company.
     *
     * This includes its own price lists and, if the company belongs
     * to a group, the price lists of other companies in the same group.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @return array List of accessible price lists.
     */
    public function getAccessiblePriceLists(int $companyId): array
    {
        // TODO: Implement getAccessiblePriceLists() method.
    }

    /**
     * Sends notifications related to product updates for a company.
     *
     * Notifications must be triggered when new products are added
     * or existing products are updated.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @param  array  $products  List of affected products.
     */
    public function notifyProductUpdates(int $companyId, array $products): void
    {
        // TODO: Implement notifyProductUpdates() method.
    }

    /**
     * Validates the subscription payment status for a company.
     *
     * This method is intended to be executed by a scheduled job.
     * If the payment is overdue, the company must be automatically disabled.
     *
     * @param  int  $companyId  Unique identifier of the company.
     * @return bool True if the subscription is up to date, false if the company was disabled.
     */
    public function validateSubscriptionPayment(int $companyId): bool
    {
        // TODO: Implement validateSubscriptionPayment() method.
    }

    private function selectFields(): array
    {
        return [
            'id',
            'name',
            'email',
            'slug',
            'owner_name',
            'phone',
            'address',
            'city',
            'state',
            'country',
            'zip_code',
            'rfc',
            'company_group_id',
            'tax_id',
            'license',
            'status',
            'billing_period',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ];
    }
}
