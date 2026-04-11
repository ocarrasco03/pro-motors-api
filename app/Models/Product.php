<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use Blamable, HasFactory, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'sku',
        'brand_id',
        'description',
        'alternative_sku',
        'application',
        'ean',
        'sat_code',
        'attributes',
        'is_active',
    ];

    /** @var list<string> */
    protected $hidden = [
        'created_by',
        'updated_by',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function alternativeBrand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'alternative_brand_id');
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
            ->withPivot([
                'supplier_sku',
                'supplier_cost',
                'currency',
                'lead_time_days',
                'minimum_order_quantity',
                'is_preferred',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function priceLists(): BelongsToMany
    {
        return $this->belongsToMany(PriceList::class)
            ->using(PriceListProduct::class)
            ->withPivot([
                'supplier_id',
                'currency',
                'cost',
                'discount',
                'apply_discount',
            ])
            ->withTimestamps();
    }

    public function equivalences(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_equivalences',
            'product_id',
            'equivalent_product_id'
        )->withPivot(['relation_type', 'notes']);
    }

    public function equivalentTo(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_equivalences',
            'equivalent_product_id',
            'product_id'
        )->withPivot(['relation_type', 'notes']);
    }

    public function relations(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_relations',
            'product_id',
            'related_product_id'
        )->withPivot(['relation_type', 'notes']);
    }

    public function relatedTo(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_relations',
            'related_product_id',
            'product_id'
        )->withPivot(['relation_type', 'notes']);
    }

    public function history(): HasMany
    {
        return $this->hasMany(ProductHistory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBrand($query, Brand|int $brand)
    {
        return $query->where('brand_id', $brand instanceof Brand ? $brand->id : $brand);
    }

    public function scopeBySku($query, string $sku)
    {
        return $query->where('sku', $sku);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('sku', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%")
                ->orWhere('alternative_sku', 'LIKE', "%{$term}%")
                ->orWhereHas('brand', fn ($bq) => $bq->where('name', 'LIKE', "%{$term}%"));
        });
    }
}
