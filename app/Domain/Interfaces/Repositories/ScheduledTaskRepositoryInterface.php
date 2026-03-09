<?php

namespace App\Domain\Interfaces\Repositories;

use App\Models\ScheduledTask;
use Illuminate\Pagination\LengthAwarePaginator;

interface ScheduledTaskRepositoryInterface
{
    public function findById(int $id): ?ScheduledTask;

    public function paginateAll(array $filters = []): LengthAwarePaginator;

    public function create(array $data): ScheduledTask;

    public function update(ScheduledTask $scheduledTask, array $data): ScheduledTask;

    public function delete(ScheduledTask $scheduledTask): bool;

    public function getActiveTasks(): \Illuminate\Database\Eloquent\Collection;
}
