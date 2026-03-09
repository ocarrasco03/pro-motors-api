<?php

namespace App\Http\Resources\Scheduler;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledTaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'jobClass' => $this->job_class,
            'command' => $this->command,
            'cronExpression' => $this->cron_expression,
            'frequency' => $this->frequency,
            'frequencyTime' => $this->frequency_time,
            'payload' => $this->payload,
            'timezone' => $this->timezone,
            'isActive' => $this->is_active,
            'withoutOverlapping' => $this->without_overlapping,
            'runInBackground' => $this->run_in_background,
            'lastRunAt' => $this->last_run_at,
            'nextRunAt' => $this->next_run_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'createdBy' => $this->created_by,
            'updatedBy' => $this->updated_by,
            'runs' => $this->whenLoaded(
                'scheduledTaskRun',
                fn () => ScheduledTaskRunResource::collection($this->scheduledTaskRun)
            ),
        ];
    }
}
