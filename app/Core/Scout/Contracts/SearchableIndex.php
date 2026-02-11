<?php

namespace App\Core\Scout\Contracts;

interface SearchableIndex
{
    public static function indexName(): string;
    public static function filterable(): array;
    public static function sortable(): array;
    public static function searchable(): array;
}
