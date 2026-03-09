<?php

namespace App\Application\Services\ScheduledTask;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\ScheduledTask\CreateScheduledTaskDTO;
use App\Application\DTOs\ScheduledTask\UpdateScheduledTaskDTO;
use App\Models\ScheduledTask;
use Illuminate\Pagination\LengthAwarePaginator;

interface ScheduledTaskService
{
    public function getAll(SearchDTO $filters): LengthAwarePaginator;

    public function getScheduledTask(ScheduledTask $scheduledTask): ScheduledTask;

    public function create(CreateScheduledTaskDTO $data): ScheduledTask;

    public function update(ScheduledTask $scheduledTask, UpdateScheduledTaskDTO $data): ScheduledTask;

    public function delete(ScheduledTask $scheduledTask): bool;

    public function toggle(ScheduledTask $scheduledTask): ScheduledTask;
}
