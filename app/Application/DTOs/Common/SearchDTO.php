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

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            sortBy: $data['sort_by'] ?? 'id',
            orderBy: $data['order_by'] ?? 'asc',
            perPage: $data['per_page'] ?? 10,
            authUser: auth()->user(),
            page: $data['page'] ?? null,
            filterBy: $data['filter_by'] ?? null
        );
    }
}
