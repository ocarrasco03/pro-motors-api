<?php

namespace App\Data\Repositories;

use App\Domain\Interfaces\Repositories\CompanyRepositoryInterface;
use App\Models\Company;
use App\Models\CompanyGroup;
use App\Models\Tax;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final class CompanyRepository implements CompanyRepositoryInterface
{
    public function findById(int $id): ?Company
    {
        return Company::with(['companyGroup', 'tax'])->find($id);
    }

    public function findBySlug(string $slug): ?Company
    {
        return Company::with(['companyGroup', 'tax'])->where('slug', $slug)->first();
    }

    public function paginateAccessibleBy(User $user, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 10;
        $sortBy = $filters['sort_by'] ?? 'id';
        $orderBy = $filters['order_by'] ?? 'asc';
        $search = $filters['search'] ?? '';

        if ($search && method_exists(Company::class, 'search')) {
            return Company::search($search, function ($engine, $query, $options) use ($user) {
                    $options['filter'] = 'company_id = ' . $user->company_id;
                    return $engine->search($query, $options);
                })
                ->with(['companyGroup:id,name', 'tax:id,name,type,rate'])
                ->paginate($perPage);
        }

        $query = Company::query()
            ->accessibleBy($user)
            ->with(['companyGroup:id,name', 'tax:id,name,type,rate'])
            ->orderBy($sortBy, $orderBy);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('owner_name', 'like', '%' . $search . '%')
                    ->orWhere('rfc', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy($sortBy, $orderBy)->paginate($perPage);
    }

    public function create(array $data): Company
    {
        try {
            DB::beginTransaction();

            $data['company_group_id'] = $this->resolveCompanyGroupId($data);
            if (isset($data['company_group_name'])) {
                unset($data['company_group_name']);
            }

            $data['tax_id'] = $this->resolveTaxId($data);
            if (isset($data['tax_name'])) {
                unset($data['tax_name']);
            }

            $company = Company::create($data);

            DB::commit();

            Log::info("Company created with ID: {$company->id}");

            return $company->load(['companyGroup', 'tax']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to create company: " . $e->getMessage());
            throw $e;
        }
    }

    public function update(Company $company, array $data): Company
    {
        try {
            DB::beginTransaction();

            if (isset($data['company_group_id']) || isset($data['company_group_name'])) {
                $data['company_group_id'] = $this->resolveCompanyGroupId($data);

                if (isset($data['company_group_name'])) {
                    unset($data['company_group_name']);
                }
            }

            if (isset($data['tax_id']) || isset($data['tax_name'])) {
                $data['tax_id'] = $this->resolveTaxId($data);

                if (isset($data['tax_name'])) {
                    unset($data['tax_name']);
                }
            }

            $company->update($data);

            DB::commit();

            Log::info("Company with ID {$company->id} has been updated.");

            return $company->refresh()->load(['companyGroup', 'tax']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update company with ID {$company->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete(Company $company): bool
    {
        if ($company->is_protected) {
            Log::warning("Attempt to delete protected company with ID {$company->id}.");
            throw new InvalidArgumentException('This company cannot be deleted.');
        }

        if ($company->users()->count() > 0) {
            Log::warning("Attempt to delete company with ID {$company->id} that has associated users.");
            throw new InvalidArgumentException('Cannot delete a company that has associated users. Remove or reassign users before deleting the company.');
        }

        $deleted = $company->delete();

        if ($deleted) {
            Log::warning("Company with ID {$company->id} has been deleted.");
        }

        return $deleted;
    }

    public function assignToGroup(Company $company, int $groupId): Company
    {
        $company->update(['company_group_id' => $groupId]);

        Log::info("Company with ID {$company->id} assigned to group ID {$groupId}.");

        return $company->fresh(['companyGroup', 'tax']);
    }

    private function resolveCompanyGroupId(array $data): ?int
    {
        if (isset($data['company_group_id'])) {
            return (int) $data['company_group_id'];
        }

        if (isset($data['company_group_name'])) {
            $group = CompanyGroup::where('name', $data['company_group_name'])->firstOrFail();
            unset($data['company_group_name']);
            return $group->id;
        }

        return null;
    }

    private function resolveTaxId(array $data): ?int
    {
        if (isset($data['tax_id'])) {
            return (int) $data['tax_id'];
        }

        if (isset($data['tax_name'])) {
            $tax = Tax::where('name', $data['tax_name'])->firstOrFail();
            unset($data['tax_name']);
            return $tax->id;
        }

        throw new InvalidArgumentException('Tax ID is required for company creation.');
    }
}
