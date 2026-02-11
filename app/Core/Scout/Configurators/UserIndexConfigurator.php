<?php

namespace App\Core\Scout\Configurators;

use App\Core\Scout\Contracts\SearchableIndex;

final class UserIndexConfigurator implements SearchableIndex
{

    public static function indexName(): string
    {
        return 'users';
    }

    public static function filterable(): array
    {
        return [
            'company_id',
        ];
    }

    public static function sortable(): array
    {
        return [
            'id',
            'first_name',
            'created_at',
            'updated_at',
        ];
    }

    public static function searchable(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'username',
        ];
    }
}
