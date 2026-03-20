<?php

namespace App\Application\DTOs\ScheduledTask;

use App\Data\Casts\EnumCaster;
use App\Domain\ValueObjects\Enums\SchedulerFrequencyEnum;

final readonly class UpdateScheduledTaskDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $jobClass = null,
        public ?string $command = null,
        public ?string $frequency = null,
        public ?string $frequencyTime = null,
        public ?array $payload = null,
        public ?string $timezone = null,
        public ?bool $isActive = null,
        public ?bool $withoutOverlapping = null,
        public ?bool $runInBackground = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            jobClass: $data['jobClass'] ?? null,
            command: $data['command'] ?? null,
            frequency: $data['frequency'] ?? null,
            frequencyTime: $data['frequencyTime'] ?? null,
            payload: $data['payload'] ?? null,
            timezone: $data['timezone'] ?? null,
            isActive: $data['isActive'] ?? null,
            withoutOverlapping: $data['withoutOverlapping'] ?? null,
            runInBackground: $data['runInBackground'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->jobClass !== null) {
            $data['job_class'] = $this->jobClass;
        }
        if ($this->command !== null) {
            $data['command'] = $this->command;
        }
        if ($this->frequency !== null) {
            $frequency = EnumCaster::cast(SchedulerFrequencyEnum::class, $this->frequency, null);
            $data['cron_expression'] = $frequency instanceof SchedulerFrequencyEnum
                ? $frequency->getCronExpression($this->frequencyTime)
                : $this->frequency;
            $data['frequency'] = $this->frequency;
            $data['frequency_time'] = $this->frequencyTime;
        }
        if ($this->payload !== null) {
            $data['payload'] = $this->payload;
        }
        if ($this->timezone !== null) {
            $data['timezone'] = $this->timezone;
        }
        if ($this->isActive !== null) {
            $data['is_active'] = $this->isActive;
        }
        if ($this->withoutOverlapping !== null) {
            $data['without_overlapping'] = $this->withoutOverlapping;
        }
        if ($this->runInBackground !== null) {
            $data['run_in_background'] = $this->runInBackground;
        }

        return $data;
    }
}
