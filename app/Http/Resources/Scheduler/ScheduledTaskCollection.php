<?php

namespace App\Http\Resources\Scheduler;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ScheduledTaskCollection extends ResourceCollection
{
    public $collects = ScheduledTaskResource::class;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
