<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\Blamable;
use App\Support\Traits\HasSlug;
use App\Support\Traits\Slug\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceList extends Model
{
    use Blamable, HasFactory, HasSlug, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'is_default',
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
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn () => "{$this->company->name}-{$this->name}")
            ->saveSlugsTo('slug')
            ->usingSeparator('-');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForCompany($query, Company|int $company)
    {
        return $query->where('company_id', $company instanceof Company ? $company->id : $company);
    }
}
