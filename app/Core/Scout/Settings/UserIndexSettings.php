<?php

namespace App\Core\Scout\Settings;

final class UserIndexSettings
{
    public static function filterable(): array
    {
        return [
            'first_name',
            'last_name',
            'company_id',
            'active',
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

    public static function sortable(): array
    {
        return [
            'id',
            'created_at',
        ];
    }
}
