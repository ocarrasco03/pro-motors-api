<?php

namespace App\Models;

use App\Core\Enums\BillingPeriodEnum;
use App\Core\Enums\LicenseEnum;
use App\Core\Enums\StatusEnum;
use App\Core\Traits\Blamable;
use App\Core\Traits\HasSlug;
use App\Core\Traits\Slug\SlugOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, HasSlug, Blamable;

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
        'zip_code',
        'rfc',
        'company_group_id',
        'tax_id',
        'license',
        'status',
        'billing_period',
        'is_protected',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_by',
        'updated_by',
        'tax_id',
        'license',
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
        'is_protected' => 'boolean',
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

    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            $q->where('id', $user->company_id)
                ->orWhere('company_group_id', $user->company->company_group_id);
        });
    }

    public function isAccessibleBy(User $user): bool
    {
        if ($this->status !== StatusEnum::ACTIVE) {
            return false;
        }

        if ($this->id === $user->company_id) {
            return true;
        }

        return false;
    }

    public function isEditableBy(User $user): bool
    {
        if ($this->is_protected) {
            return false;
        }

        if ($this->status !== StatusEnum::ACTIVE) {
            return false;
        }

        if (! $user->company) {
            return false;
        }

        if ($this->id === $user->company_id) {
            return true;
        }

        return false;
    }
}
