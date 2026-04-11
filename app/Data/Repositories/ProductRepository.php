<?php

declare(strict_types=1);

namespace App\Data\Repositories;

use App\Domain\Interfaces\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\ProductEquivalence;
use App\Models\ProductRelation;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::with(['brand', 'suppliers', 'priceLists'])->find($id);
    }

    public function findBySkuAndBrand(string $sku, int $brandId, string $description): ?Product
    {
        return Product::with(['brand'])
            ->where('sku', $sku)
            ->where('brand_id', $brandId)
            ->where('description', $description)
            ->first();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $sortBy = $filters['sort_by'] ?? 'id';
        $orderBy = $filters['order_by'] ?? 'desc';
        $search = $filters['search'] ?? '';
        $brandId = $filters['brand_id'] ?? null;
        $activeOnly = $filters['active_only'] ?? false;

        $query = Product::with(['brand'])
            ->orderBy($sortBy, $orderBy);

        if ($activeOnly) {
            $query->active();
        }

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        if ($search) {
            $query->search($search);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Product
    {
        try {
            DB::beginTransaction();

            $product = Product::create($data);

            DB::commit();

            Log::info("Product created with ID: {$product->id}");

            return $product->load(['brand']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(Product $product, array $data): Product
    {
        try {
            DB::beginTransaction();

            $product->update($data);

            DB::commit();

            Log::info("Product with ID {$product->id} has been updated.");

            return $product->fresh(['brand']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update product with ID {$product->id}: ".$e->getMessage());
            throw $e;
        }
    }

    public function delete(Product $product): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $product->delete();

            DB::commit();

            if ($deleted) {
                Log::info("Product with ID {$product->id} has been deleted.");
            }

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete product with ID {$product->id}: ".$e->getMessage());
            throw $e;
        }
    }

    public function addEquivalence(Product $product, array $data): Product
    {
        try {
            DB::beginTransaction();

            ProductEquivalence::firstOrCreate([
                'product_id' => $product->id,
                'equivalent_product_id' => $data['equivalent_product_id'],
            ], [
                'relation_type' => $data['relation_type'] ?? 'equivalent',
                'notes' => $data['notes'] ?? null,
            ]);

            DB::commit();

            Log::info("Equivalence added for product ID {$product->id}");

            return $product->fresh(['brand', 'equivalences.product.brand']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add equivalence: '.$e->getMessage());
            throw $e;
        }
    }

    public function removeEquivalence(Product $product, int $equivalentProductId): bool
    {
        try {
            DB::beginTransaction();

            $deleted = ProductEquivalence::where('product_id', $product->id)
                ->where('equivalent_product_id', $equivalentProductId)
                ->delete();

            DB::commit();

            if ($deleted) {
                Log::info("Equivalence removed for product ID {$product->id}");
            }

            return $deleted > 0;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove equivalence: '.$e->getMessage());
            throw $e;
        }
    }

    public function addRelation(Product $product, array $data): Product
    {
        try {
            DB::beginTransaction();

            ProductRelation::firstOrCreate([
                'product_id' => $product->id,
                'related_product_id' => $data['related_product_id'],
            ], [
                'relation_type' => $data['relation_type'] ?? 'accessory',
                'notes' => $data['notes'] ?? null,
            ]);

            DB::commit();

            Log::info("Relation added for product ID {$product->id}");

            return $product->fresh(['brand', 'relations.product.brand']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add relation: '.$e->getMessage());
            throw $e;
        }
    }

    public function removeRelation(Product $product, int $relatedProductId): bool
    {
        try {
            DB::beginTransaction();

            $deleted = ProductRelation::where('product_id', $product->id)
                ->where('related_product_id', $relatedProductId)
                ->delete();

            DB::commit();

            if ($deleted) {
                Log::info("Relation removed for product ID {$product->id}");
            }

            return $deleted > 0;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove relation: '.$e->getMessage());
            throw $e;
        }
    }
}
