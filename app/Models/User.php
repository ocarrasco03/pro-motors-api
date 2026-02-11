<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Core\Enums\RolesEnum;
use App\Core\Traits\Blamable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Blamable, HasApiTokens, HasFactory, HasRoles, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'active',
        'created_by',
        'updated_by',
        'last_login_at',
        'company_id',
    ];

    protected $appends = [
        'all_permissions',
        'full_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAllPermissionsAttribute(): Collection
    {
        return $this->getAllPermissions()
            ->unique('id')
            ->values();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        if (is_null($user->company_id) && $user->hasRole(RolesEnum::SUPER_ADMIN->value)) {
            return $query;
        }

        if (! is_null($user->company_id)) {
            return $query->where('company_id', $user->company_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'active' => $this->active,
        ];
    }

    public function canSeeUser(User $user): bool
    {
        if ($this->hasRole(RolesEnum::SUPER_ADMIN->value) && $this->company_id === null) {
            return true;
        }

        if ($this->company_id !== null && $this->company_id === $user->company_id) {
            return true;
        }

        return false;
    }

    public function isEditableFor(User $user): bool
    {
        if ($this->hasRole(RolesEnum::SUPER_ADMIN->value) && $this->company_id === null) {
            return true;
        }

        if ($this->id === $user->id) {
            return true;
        }

        if ($this->company_id !== null && $this->company_id === $user->company_id) {
            return true;
        }

        return false;
    }

    public function isDestroyableFor(User $user): bool
    {
        if ($this->id === $user->id) {
            return false;
        }

        if ($this->company_id === $user->company_id) {
            return true;
        }

        return false;
    }
}
