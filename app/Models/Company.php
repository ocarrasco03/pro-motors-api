<?php

namespace App\Models;

use App\Domain\ValueObjects\Enums\BillingPeriodEnum;
use App\Domain\ValueObjects\Enums\LicenseEnum;
use App\Domain\ValueObjects\Enums\RolesEnum;
use App\Domain\ValueObjects\Enums\StatusEnum;
use App\Support\Traits\Blamable;
use App\Support\Traits\HasSlug;
use App\Support\Traits\Slug\SlugOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use Blamable, HasFactory, HasSlug;

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
        'cutoff_date',
        'grace_period_days',
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
        'is_protected',
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
        'cutoff_date' => 'date',
        'grace_period_days' => 'integer',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return static::findOrFail((int) $value);
        }

        return static::where('slug', $value)->firstOrFail();
    }

    /**
     * Get the options for generating the slug.
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->hasRole(RolesEnum::SUPER_ADMIN) && is_null($user->company_id)) {
            return $query;
        }

        if (is_null($user->company_id)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('id', $user->company_id)
            ->when($user->company->company_group_id, function ($query) use ($user) {
                $query->orWhere('company_group_id', $user->company->company_group_id);
            });
    }

    public function isAccessibleBy(User $user): bool
    {
        if (is_null($user->company_id) && $user->hasRole(RolesEnum::SUPER_ADMIN)) {
            return true;
        }

        if (! is_null($user->company_id) && $this->id === $user->company_id) {
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

    public function isActive(): bool
    {
        return $this->status === StatusEnum::ACTIVE;
    }

    public function getGracePeriodEndDate(): ?\Carbon\Carbon
    {
        if (! $this->cutoff_date) {
            return null;
        }

        return \Carbon\Carbon::parse($this->cutoff_date)->addDays($this->grace_period_days ?? 5);
    }

    public function isWithinGracePeriod(): bool
    {
        $gracePeriodEnd = $this->getGracePeriodEndDate();

        if (! $gracePeriodEnd) {
            return false;
        }

        return now()->lessThanOrEqualTo($gracePeriodEnd);
    }

    public function hasCurrentMonthPayment(): bool
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return $this->payments()
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->exists();
    }

    public function shouldBeSuspended(): bool
    {
        if ($this->status !== StatusEnum::ACTIVE) {
            return false;
        }

        if ($this->hasCurrentMonthPayment()) {
            return false;
        }

        return ! $this->isWithinGracePeriod();
    }
}
