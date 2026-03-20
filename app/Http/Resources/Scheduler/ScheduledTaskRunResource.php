<?php

namespace App\Http\Resources\Scheduler;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledTaskRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'scheduledTaskId' => $this->scheduled_task_id,
            'startedAt' => $this->started_at,
            'endedAt' => $this->ended_at,
            'status' => $this->status,
            'error' => $this->error,
            'startedBy' => $this->started_by,
            'duration' => $this->started_at && $this->ended_at
                ? $this->started_at->diffInSeconds($this->ended_at)
                : null,
        ];
    }
}
