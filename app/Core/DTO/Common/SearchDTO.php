<?php

namespace App\Core\DTO\Common;

use App\Models\User;
use function Laravel\Prompts\search;

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
