<?php

namespace App\Infrastructure\Search\Meilisearch\Specifications;

use App\Infrastructure\Search\Meilisearch\Specifications\UserSpecification;
use App\Models\User;

final class CompanyScopeSpecification implements UserSpecification
{
    public function __construct(private User $authUser) {}

    public function apply($query): void
    {
        if ($this->authUser->company_id) {
            $query->where('company_id', $this->authUser->company_id);
        }
    }
}
