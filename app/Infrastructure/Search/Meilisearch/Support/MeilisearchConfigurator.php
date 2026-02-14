<?php

namespace App\Infrastructure\Search\Meilisearch\Support;

use App\Infrastructure\Search\Meilisearch\Contracts\SearchableIndex;
use Laravel\Scout\EngineManager;

class MeilisearchConfigurator
{
    public function configure(SearchableIndex $config): void
    {
        $engine = app(EngineManager::class)->engine();
        $index = $engine->index($config::indexName());

        $index->updateFilterableAttributes($config::filterable());
        $index->updateSortableAttributes($config::sortable());
        $index->updateSearchableAttributes($config::searchable());
    }
}
