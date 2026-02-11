<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;

class ConfigureMeilisearchIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:configure-meilisearch-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(EngineManager $manager)
    {
        $engine = $manager->engine();
        $index = $engine->index('users');

        $index->updateFilterableAttributes([
            'company_id',
            'active',
        ]);

        $index->updateSortableAttributes([
            'id',
            'created_at',
        ]);
    }
}
