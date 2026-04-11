<?php

declare(strict_types=1);

namespace App\Data\Repositories;

use App\Domain\Interfaces\Repositories\BrandRepositoryInterface;
use App\Models\Brand;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class BrandRepository implements BrandRepositoryInterface
{
    public function findById(int $id): ?Brand
    {
        return Brand::find($id);
    }

    public function findBySlug(string $slug): ?Brand
    {
        return Brand::where('slug', $slug)->first();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $sortBy = $filters['sort_by'] ?? 'name';
        $orderBy = $filters['order_by'] ?? 'asc';
        $search = $filters['search'] ?? '';
        $activeOnly = $filters['active_only'] ?? false;

        $query = Brand::query()->orderBy($sortBy, $orderBy);

        if ($activeOnly) {
            $query->active();
        }

        if ($search) {
            $query->where('name', 'LIKE', '%'.$search.'%');
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Brand
    {
        try {
            DB::beginTransaction();

            $brand = Brand::create($data);

            DB::commit();

            Log::info("Brand created with ID: {$brand->id}");

            return $brand;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create brand: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(Brand $brand, array $data): Brand
    {
        try {
            DB::beginTransaction();

            $brand->update($data);

            DB::commit();

            Log::info("Brand with ID {$brand->id} has been updated.");

            return $brand->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update brand with ID {$brand->id}: ".$e->getMessage());
            throw $e;
        }
    }

    public function delete(Brand $brand): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $brand->delete();

            DB::commit();

            if ($deleted) {
                Log::info("Brand with ID {$brand->id} has been deleted.");
            }

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete brand with ID {$brand->id}: ".$e->getMessage());
            throw $e;
        }
    }
}
