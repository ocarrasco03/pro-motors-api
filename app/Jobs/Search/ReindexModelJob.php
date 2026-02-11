<?php

namespace App\Jobs\Search;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class ReindexModelJob implements ShouldQueue
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $modelClass,
        protected array $ids
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->modelClass::whereIn('id', $this->ids)
            ->searchable();
    }
}
