<?php

namespace App\Http\Requests\Scheduler;

use App\Domain\ValueObjects\Enums\SchedulerFrequencyEnum;
use App\Models\ScheduledTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduledTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $scheduledTask = $this->route('scheduledTask');

        return $this->user()->can('create', $scheduledTask ?? ScheduledTask::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'jobClass' => ['required', 'string', 'max:255'],
            'command' => ['nullable', 'string', 'max:500'],
            'frequency' => ['required', 'string', Rule::in(array_column(SchedulerFrequencyEnum::cases(), 'value'))],
            'frequencyTime' => ['nullable', 'string', 'max:50'],
            'payload' => ['nullable', 'array'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'isActive' => ['nullable', 'boolean'],
            'withoutOverlapping' => ['nullable', 'boolean'],
            'runInBackground' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Scheduled task name is required.',
            'name.string' => 'Scheduled task name must be a string.',
            'name.max' => 'Scheduled task name must not exceed 255 characters.',
            'jobClass.required' => 'Job class is required.',
            'jobClass.string' => 'Job class must be a string.',
            'frequency.required' => 'Frequency is required.',
            'frequency.in' => 'Invalid frequency selected.',
        ];
    }
}
