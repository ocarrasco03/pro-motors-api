<?php

namespace App\Models;

use App\Core\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledTaskRun extends Model
{
    protected $table = 'scheduled_task_runs';

    protected $fillable = [
        'scheduled_task_id',
        'started_at',
        'ended_at',
        'status',
        'error',
        'started_by'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'status' => StatusEnum::class,
    ];

    public function scheduledTask(): BelongsTo
    {
        return $this->belongsTo(ScheduledTask::class);
    }
}
