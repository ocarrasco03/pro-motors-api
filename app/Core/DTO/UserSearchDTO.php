<?php

namespace App\Core\DTO;

use App\Models\User;

final class UserSearchDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly int $perPage,
        public readonly string $sortBy,
        public readonly string $orderBy,
        public readonly ?User $user,
    ) {}

    public static function fromRequest(): self
    {
        return new self(
            search: request()->get('search'),
            perPage: request()->integer('perPage', 10),
            sortBy: request()->integer('sortBy', 'id'),
            orderBy: request()->integer('orderBy', 'asc'),
            user: auth()->user(),
        );
    }
}
