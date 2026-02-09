<?php

namespace App\Core\DTO;

use App\Models\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class UserSearchDTO
{
    public function __construct(
        public ?string $search,
        public int     $perPage,
        public string  $sortBy,
        public string  $orderBy,
        public ?User   $user,
    ) {}

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public static function fromRequest(): self
    {
        return new self(
            search: request()->get('search'),
            perPage: request()->get('perPage', 10),
            sortBy: request()->get('sortBy', 'id'),
            orderBy: request()->get('orderBy', 'asc'),
            user: request()->user(),
        );
    }
}
