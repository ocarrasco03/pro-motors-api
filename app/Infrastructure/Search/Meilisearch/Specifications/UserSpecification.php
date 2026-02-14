<?php

namespace App\Infrastructure\Search\Meilisearch\Specifications;

interface UserSpecification
{
    public function apply($query): void;
}
