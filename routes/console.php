<?php

use App\Models\ScheduledTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Cache::lock('scheduler-lock', 55)->get(function ($task) {
        ScheduledTask::active()->get()->each(function (ScheduledTask $task) {
            $event = Schedule::job(new ($task->job_class)($task->payload))
                ->cron($task->cron_expression)
                ->timezone($task->timezone);

            if ($task->without_overlapping) {
                $event->withoutOverlapping();
            }

            if ($task->run_in_background) {
                $event->runInBackground();
            }
        });
    });
})->everyMinute();
