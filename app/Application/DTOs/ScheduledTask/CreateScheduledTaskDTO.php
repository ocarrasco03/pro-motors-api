<?php

namespace App\Application\DTOs\ScheduledTask;

use App\Data\Casts\EnumCaster;
use App\Domain\ValueObjects\Enums\SchedulerFrequencyEnum;
use InvalidArgumentException;

final readonly class CreateScheduledTaskDTO
{
    public function __construct(
        public string $name,
        public string $jobClass,
        public ?string $command,
        public string $frequency,
        public ?string $frequencyTime = null,
        public ?array $payload = null,
        public ?string $timezone = null,
        public bool $isActive = true,
        public bool $withoutOverlapping = false,
        public bool $runInBackground = false,
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Scheduled task name is required');
        }

        if (empty($this->jobClass)) {
            throw new InvalidArgumentException('Job class is required');
        }

        if (empty($this->frequency)) {
            throw new InvalidArgumentException('Frequency is required');
        }
    }

    public static function fromArray(array $data): self
    {
        $frequency = EnumCaster::cast(
            SchedulerFrequencyEnum::class,
            $data['frequency'] ?? null,
            null
        );

        $cronExpression = $frequency instanceof SchedulerFrequencyEnum
            ? $frequency->getCronExpression($data['frequencyTime'] ?? null)
            : ($data['cronExpression'] ?? null);

        if (empty($cronExpression)) {
            throw new InvalidArgumentException('Could not generate cron expression');
        }

        return new self(
            name: $data['name'],
            jobClass: $data['jobClass'],
            command: $data['command'] ?? null,
            frequency: $data['frequency'],
            frequencyTime: $data['frequencyTime'] ?? null,
            payload: $data['payload'] ?? null,
            timezone: $data['timezone'] ?? null,
            isActive: $data['isActive'] ?? true,
            withoutOverlapping: $data['withoutOverlapping'] ?? false,
            runInBackground: $data['runInBackground'] ?? false,
        );
    }

    public function toArray(): array
    {
        $frequency = EnumCaster::cast(SchedulerFrequencyEnum::class, $this->frequency, null);
        $cronExpression = $frequency instanceof SchedulerFrequencyEnum
            ? $frequency->getCronExpression($this->frequencyTime)
            : $this->frequency;

        return [
            'name' => $this->name,
            'job_class' => $this->jobClass,
            'command' => $this->command,
            'cron_expression' => $cronExpression,
            'frequency' => $this->frequency,
            'frequency_time' => $this->frequencyTime,
            'payload' => $this->payload,
            'timezone' => $this->timezone,
            'is_active' => $this->isActive,
            'without_overlapping' => $this->withoutOverlapping,
            'run_in_background' => $this->runInBackground,
        ];
    }
}
