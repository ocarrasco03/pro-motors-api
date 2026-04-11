<?php

declare(strict_types=1);

namespace App\Data\Repositories;

use App\Domain\Interfaces\Repositories\SupplierRepositoryInterface;
use App\Models\Supplier;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class SupplierRepository implements SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier
    {
        return Supplier::find($id);
    }

    public function findBySlug(string $slug): ?Supplier
    {
        return Supplier::where('slug', $slug)->first();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $sortBy = $filters['sort_by'] ?? 'name';
        $orderBy = $filters['order_by'] ?? 'asc';
        $search = $filters['search'] ?? '';
        $activeOnly = $filters['active_only'] ?? false;

        $query = Supplier::query()->orderBy($sortBy, $orderBy);

        if ($activeOnly) {
            $query->active();
        }

        if ($search) {
            $query->where('name', 'LIKE', '%'.$search.'%');
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Supplier
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::create($data);

            DB::commit();

            Log::info("Supplier created with ID: {$supplier->id}");

            return $supplier;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create supplier: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        try {
            DB::beginTransaction();

            $supplier->update($data);

            DB::commit();

            Log::info("Supplier with ID {$supplier->id} has been updated.");

            return $supplier->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update supplier with ID {$supplier->id}: ".$e->getMessage());
            throw $e;
        }
    }

    public function delete(Supplier $supplier): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $supplier->delete();

            DB::commit();

            if ($deleted) {
                Log::info("Supplier with ID {$supplier->id} has been deleted.");
            }

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete supplier with ID {$supplier->id}: ".$e->getMessage());
            throw $e;
        }
    }
}
