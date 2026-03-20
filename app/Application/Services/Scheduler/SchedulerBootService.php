<?php

namespace App\Application\Services\Scheduler;

interface SchedulerBootService
{
    public function registerDefaultTasks(): void;
}
