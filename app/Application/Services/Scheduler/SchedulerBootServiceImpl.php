<?php

namespace App\Application\Services\Scheduler;

use App\Domain\ValueObjects\Enums\SchedulerFrequencyEnum;
use App\Models\ScheduledTask;
use Illuminate\Support\Facades\Log;

class SchedulerBootServiceImpl implements SchedulerBootService
{
    public function registerDefaultTasks(): void
    {
        $defaultTasks = $this->getDefaultTasks();

        foreach ($defaultTasks as $taskData) {
            $exists = ScheduledTask::where('job_class', $taskData['job_class'])->exists();

            if (! $exists) {
                ScheduledTask::create($taskData);
                Log::info("Default scheduled task registered: {$taskData['name']}");
            }
        }
    }

    private function getDefaultTasks(): array
    {
        return [
            [
                'name' => 'Clean Old Scheduled Task Runs',
                'job_class' => 'App\Jobs\CleanOldScheduledTaskRunsJob',
                'command' => 'scheduled-tasks:clean-old-runs',
                'cron_expression' => SchedulerFrequencyEnum::DAILY->getCronExpression('02:00'),
                'frequency' => SchedulerFrequencyEnum::DAILY->value,
                'frequency_time' => '02:00',
                'payload' => ['days' => 30],
                'timezone' => 'America/Mexico_City',
                'is_active' => true,
                'without_overlapping' => true,
                'run_in_background' => false,
            ],
            [
                'name' => 'Check Company Subscriptions',
                'job_class' => 'App\Jobs\CheckCompanySubscriptionJob',
                'command' => 'subscriptions:check',
                'cron_expression' => SchedulerFrequencyEnum::DAILY->getCronExpression('22:00'),
                'frequency' => SchedulerFrequencyEnum::DAILY->value,
                'frequency_time' => '22:00',
                'payload' => null,
                'timezone' => 'America/Mexico_City',
                'is_active' => true,
                'without_overlapping' => true,
                'run_in_background' => false,
            ],
        ];
    }
}
