<?php

namespace App\Models;

use App\Core\Enums\BillingPeriodEnum;
use App\Core\Enums\LicenseEnum;
use App\Core\Enums\StatusEnum;
use App\Core\Traits\HasSlug;
use App\Core\Traits\Slug\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'owner_name',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'rfc',
        'company_group_id',
        'tax_id',
        'license',
        'status',
        'billing_period',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'status' => StatusEnum::class,
        'billing_period' => BillingPeriodEnum::class,
        'license' => LicenseEnum::class,
        'zip' => 'numeric'
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the options for generating the slug.
     *
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingSeparator('-');
    }

    public function companyGroup(): BelongsTo
    {
        return $this->belongsTo(CompanyGroup::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }


}
