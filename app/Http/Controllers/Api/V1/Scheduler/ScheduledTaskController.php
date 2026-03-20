<?php

namespace App\Http\Controllers\Api\V1\Scheduler;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\ScheduledTask\CreateScheduledTaskDTO;
use App\Application\DTOs\ScheduledTask\UpdateScheduledTaskDTO;
use App\Application\Services\ScheduledTask\ScheduledTaskService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Scheduler\ScheduledTaskRequest;
use App\Http\Requests\Scheduler\ScheduledTaskUpdateRequest;
use App\Http\Resources\Scheduler\ScheduledTaskCollection;
use App\Http\Resources\Scheduler\ScheduledTaskResource;
use App\Models\ScheduledTask;

class ScheduledTaskController extends Controller
{
    public function __construct(protected ScheduledTaskService $scheduledTaskService)
    {
        $this->authorizeResource(ScheduledTask::class, 'scheduledTask');
    }

    public function index(SearchRequest $request)
    {
        $dto = SearchDTO::fromArray($request->validated());
        $scheduledTasks = $this->scheduledTaskService->getAll($dto);

        return $this->success(new ScheduledTaskCollection($scheduledTasks));
    }

    public function store(ScheduledTaskRequest $request)
    {
        $dto = CreateScheduledTaskDTO::fromArray($request->validated());
        $scheduledTask = $this->scheduledTaskService->create($dto);

        return $this->success(new ScheduledTaskResource($scheduledTask), 'Scheduled task has been created.', 201);
    }

    public function show(ScheduledTask $scheduledTask)
    {
        $scheduledTask = $this->scheduledTaskService->getScheduledTask($scheduledTask);

        abort_if(! $scheduledTask, 404, 'Scheduled task not found.');

        return $this->success(new ScheduledTaskResource($scheduledTask));
    }

    public function update(ScheduledTaskUpdateRequest $request, ScheduledTask $scheduledTask)
    {
        $dto = UpdateScheduledTaskDTO::fromArray($request->validated());
        $result = $this->scheduledTaskService->update($scheduledTask, $dto);

        return $this->success(new ScheduledTaskResource($result), 'Scheduled task updated successfully.');
    }

    public function destroy(ScheduledTask $scheduledTask)
    {
        $this->scheduledTaskService->delete($scheduledTask);

        return $this->success(null, 'Scheduled task has been deleted', 204);
    }

    public function toggle(ScheduledTask $scheduledTask)
    {
        $result = $this->scheduledTaskService->toggle($scheduledTask);

        return $this->success(new ScheduledTaskResource($result), 'Scheduled task toggled successfully.');
    }
}
