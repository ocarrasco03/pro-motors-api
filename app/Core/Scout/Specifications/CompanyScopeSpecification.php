<?php

namespace App\Core\Scout\Specifications;

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
