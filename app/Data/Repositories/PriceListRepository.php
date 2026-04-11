<?php

declare(strict_types=1);

namespace App\Data\Repositories;

use App\Domain\Interfaces\Repositories\PriceListRepositoryInterface;
use App\Models\PriceList;
use App\Models\PriceListProduct;
use App\Models\ProductHistory;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class PriceListRepository implements PriceListRepositoryInterface
{
    public function findById(int $id): ?PriceList
    {
        return PriceList::with(['company', 'products.brand'])->find($id);
    }

    public function findBySlug(string $slug): ?PriceList
    {
        return PriceList::with(['company', 'products.brand'])->where('slug', $slug)->first();
    }

    public function paginateForCompany(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $sortBy = $filters['sort_by'] ?? 'name';
        $orderBy = $filters['order_by'] ?? 'asc';
        $search = $filters['search'] ?? '';
        $activeOnly = $filters['active_only'] ?? false;

        $query = PriceList::with(['company'])
            ->where('company_id', $companyId)
            ->orderBy($sortBy, $orderBy);

        if ($activeOnly) {
            $query->active();
        }

        if ($search) {
            $query->where('name', 'LIKE', '%'.$search.'%');
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): PriceList
    {
        try {
            DB::beginTransaction();

            if (isset($data['is_default']) && $data['is_default']) {
                PriceList::where('company_id', $data['company_id'])->update(['is_default' => false]);
            }

            $priceList = PriceList::create($data);

            DB::commit();

            Log::info("Price list created with ID: {$priceList->id}");

            return $priceList->load(['company']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create price list: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(PriceList $priceList, array $data): PriceList
    {
        try {
            DB::beginTransaction();

            if (isset($data['is_default']) && $data['is_default']) {
                PriceList::where('company_id', $priceList->company_id)
                    ->where('id', '!=', $priceList->id)
                    ->update(['is_default' => false]);
            }

            $priceList->update($data);

            DB::commit();

            Log::info("Price list with ID {$priceList->id} has been updated.");

            return $priceList->fresh(['company']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update price list with ID {$priceList->id}: ".$e->getMessage());
            throw $e;
        }
    }

    public function delete(PriceList $priceList): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $priceList->delete();

            DB::commit();

            if ($deleted) {
                Log::info("Price list with ID {$priceList->id} has been deleted.");
            }

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete price list with ID {$priceList->id}: ".$e->getMessage());
            throw $e;
        }
    }

    public function setProductPrice(PriceList $priceList, array $data): PriceList
    {
        try {
            DB::beginTransaction();

            $existing = PriceListProduct::where('price_list_id', $priceList->id)
                ->where('product_id', $data['product_id'])
                ->when(isset($data['supplier_id']), fn ($q) => $q->where('supplier_id', $data['supplier_id']))
                ->when(! isset($data['supplier_id']), fn ($q) => $q->whereNull('supplier_id'))
                ->first();

            $oldCost = $existing?->cost;
            $oldDiscount = $existing?->discount;
            $newCost = $data['cost'] ?? 0;
            $newDiscount = $data['discount'] ?? 0;

            $syncData = [
                'currency' => $data['currency'] ?? 'MXN',
                'cost' => $newCost,
                'discount' => $newDiscount,
                'apply_discount' => $data['apply_discount'] ?? true,
            ];

            if (isset($data['supplier_id'])) {
                $syncData['supplier_id'] = $data['supplier_id'];
            }

            $priceList->products()->syncWithoutDetaching([
                $data['product_id'] => $syncData,
            ]);

            if ($oldCost !== null && ($oldCost != $newCost || $oldDiscount != $newDiscount)) {
                ProductHistory::create([
                    'product_id' => $data['product_id'],
                    'price_list_id' => $priceList->id,
                    'supplier_id' => $data['supplier_id'] ?? null,
                    'currency' => $data['currency'] ?? 'MXN',
                    'old_cost' => $oldCost,
                    'new_cost' => $newCost,
                    'old_discount' => $oldDiscount,
                    'new_discount' => $newDiscount,
                    'change_type' => 'price_update',
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();

            Log::info("Product price set for price list ID {$priceList->id}");

            return $priceList->fresh(['company', 'products.brand']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to set product price: '.$e->getMessage());
            throw $e;
        }
    }

    public function removeProductPrice(PriceList $priceList, int $productId): bool
    {
        try {
            DB::beginTransaction();

            $detached = $priceList->products()->detach($productId);

            DB::commit();

            if ($detached) {
                Log::info("Product ID {$productId} removed from price list ID {$priceList->id}");
            }

            return $detached > 0;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove product price: '.$e->getMessage());
            throw $e;
        }
    }

    public function getDefaultForCompany(int $companyId): ?PriceList
    {
        return PriceList::where('company_id', $companyId)
            ->where('is_default', true)
            ->first();
    }
}
