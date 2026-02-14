<?php

namespace App\Models;

use App\Support\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledTask extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduledTaskFactory> */
    use HasFactory, Blamable;

    protected $fillable = [
        'name',
        'job_class',
        'command',
        'cron_expression',
        'payload',
        'timezone',
        'is_active',
        'without_overlapping',
        'run_in_background',
        'last_run_at',
        'next_run_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_active' => 'boolean',
        'without_overlapping' => 'boolean',
        'run_in_background' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function scheduledTaskRun(): HasMany
    {
        return $this->hasMany(ScheduledTaskRun::class);
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }
}
