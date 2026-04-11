<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\Blamable;
use App\Support\Traits\HasSlug;
use App\Support\Traits\Slug\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use Blamable, HasFactory, HasSlug, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'contact_name',
        'email',
        'phone',
        'notes',
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
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingSeparator('-');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
