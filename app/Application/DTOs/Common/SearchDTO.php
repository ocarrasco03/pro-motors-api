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
            sortBy: $data['sortBy'] ?? 'id',
            orderBy: $data['orderBy'] ?? 'asc',
            perPage: $data['perPage'] ?? 10,
            authUser: auth()->user(),
            page: $data['page'] ?? null,
            filterBy: $data['filterBy'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'sort_by' => $this->sortBy,
            'order_by' => $this->orderBy,
            'per_page' => $this->perPage,
            'auth_user' => $this->authUser,
            'page' => $this->page,
            'filter_by' => $this->filterBy
        ];
    }
}
