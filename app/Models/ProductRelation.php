<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\ValueObjects\Enums\RelationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRelation extends Model
{
    use HasFactory;

    protected $table = 'product_relations';

    /** @var list<string> */
    protected $fillable = [
        'product_id',
        'related_product_id',
        'relation_type',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'relation_type' => RelationTypeEnum::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }
}
