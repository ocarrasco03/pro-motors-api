<?php

namespace App\Models;

use App\Core\Enums\StatusEnum;
use App\Core\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyGroup extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyGroupFactory> */
    use HasFactory, Blamable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
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
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
