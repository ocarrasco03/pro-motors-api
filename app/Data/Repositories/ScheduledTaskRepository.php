<?php

namespace App\Data\Repositories;

use App\Domain\Interfaces\Repositories\ScheduledTaskRepositoryInterface;
use App\Models\ScheduledTask;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ScheduledTaskRepository implements ScheduledTaskRepositoryInterface
{
    public function findById(int $id): ?ScheduledTask
    {
        return ScheduledTask::with(['scheduledTaskRun'])->find($id);
    }

    public function paginateAll(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 10;
        $sortBy = $filters['sort_by'] ?? 'id';
        $orderBy = $filters['order_by'] ?? 'asc';
        $search = $filters['search'] ?? '';

        $query = ScheduledTask::query()->with(['scheduledTaskRun']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('job_class', 'like', '%'.$search.'%')
                    ->orWhere('command', 'like', '%'.$search.'%');
            });
        }

        return $query->orderBy($sortBy, $orderBy)->paginate($perPage);
    }

    public function create(array $data): ScheduledTask
    {
        try {
            DB::beginTransaction();

            $scheduledTask = ScheduledTask::create($data);

            DB::commit();

            Log::info("Scheduled task created with ID: {$scheduledTask->id}");

            return $scheduledTask;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create scheduled task: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(ScheduledTask $scheduledTask, array $data): ScheduledTask
    {
        try {
            DB::beginTransaction();

            $scheduledTask->update($data);

            DB::commit();

            Log::info("Scheduled task with ID {$scheduledTask->id} has been updated.");

            return $scheduledTask->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update scheduled task with ID {$scheduledTask->id}: ".$e->getMessage());
            throw $e;
        }
    }

    public function delete(ScheduledTask $scheduledTask): bool
    {
        $deleted = $scheduledTask->delete();

        if ($deleted) {
            Log::info("Scheduled task with ID {$scheduledTask->id} has been deleted.");
        }

        return $deleted;
    }

    public function getActiveTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return ScheduledTask::active()->get();
    }
}
