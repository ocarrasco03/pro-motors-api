<?php

namespace App\Application\Services\ScheduledTask;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\ScheduledTask\CreateScheduledTaskDTO;
use App\Application\DTOs\ScheduledTask\UpdateScheduledTaskDTO;
use App\Domain\Interfaces\Repositories\ScheduledTaskRepositoryInterface;
use App\Models\ScheduledTask;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduledTaskServiceImpl implements ScheduledTaskService
{
    public function __construct(
        protected ScheduledTaskRepositoryInterface $repository
    ) {}

    public function getAll(SearchDTO $searchDTO): LengthAwarePaginator
    {
        $filters = [
            'per_page' => $searchDTO->perPage ?? 10,
            'sort_by' => $searchDTO->sortBy ?? 'id',
            'order_by' => $searchDTO->orderBy ?? 'asc',
            'search' => $searchDTO->search ?? '',
            'filter_by' => $searchDTO->filterBy ?? [],
        ];

        return $this->repository->paginateAll($filters);
    }

    public function getScheduledTask(ScheduledTask $scheduledTask): ScheduledTask
    {
        return $scheduledTask->refresh()->loadMissing(['scheduledTaskRun']);
    }

    public function create(CreateScheduledTaskDTO $data): ScheduledTask
    {
        return $this->repository->create($data->toArray());
    }

    public function update(ScheduledTask $scheduledTask, UpdateScheduledTaskDTO $data): ScheduledTask
    {
        return $this->repository->update($scheduledTask, $data->toArray());
    }

    public function delete(ScheduledTask $scheduledTask): bool
    {
        return $this->repository->delete($scheduledTask);
    }

    public function toggle(ScheduledTask $scheduledTask): ScheduledTask
    {
        $scheduledTask->update(['is_active' => ! $scheduledTask->is_active]);

        return $scheduledTask->refresh();
    }
}
