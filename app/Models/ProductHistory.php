<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\ValueObjects\Enums\CurrencyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductHistory extends Model
{
    use HasFactory;

    protected $table = 'product_history';

    /** @var list<string> */
    protected $fillable = [
        'product_id',
        'price_list_id',
        'supplier_id',
        'currency',
        'old_cost',
        'new_cost',
        'old_discount',
        'new_discount',
        'change_type',
        'user_id',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'currency' => CurrencyEnum::class,
            'old_cost' => 'decimal:4',
            'new_cost' => 'decimal:4',
            'old_discount' => 'decimal:2',
            'new_discount' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
