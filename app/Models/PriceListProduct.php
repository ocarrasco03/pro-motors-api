<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PriceListProduct extends Pivot
{
    /** @var list<string> */
    protected $fillable = [
        'price_list_id',
        'product_id',
        'supplier_id',
        'currency',
        'cost',
        'discount',
        'apply_discount',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'cost' => 'decimal:4',
            'discount' => 'decimal:2',
            'apply_discount' => 'boolean',
        ];
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getFinalPrice(): float
    {
        if (! $this->apply_discount) {
            return (float) $this->cost;
        }

        return (float) $this->cost * (1 - ((float) $this->discount / 100));
    }
}
