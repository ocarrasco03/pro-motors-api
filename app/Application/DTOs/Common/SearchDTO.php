<?php

namespace App\Application\DTOs\Common;

use App\Models\User;

final readonly class SearchDTO
{
    public function __construct(
        public ?string $search,
        public string  $sortBy,
        public string  $orderBy,
        public int     $perPage,
        public ?User   $authUser,
        public ?int    $page,
        public ?array  $filterBy
    ) {}
}
