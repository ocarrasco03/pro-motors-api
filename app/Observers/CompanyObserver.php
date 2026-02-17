<?php

namespace App\Observers;

use App\Domain\ValueObjects\Enums\StatusEnum;
use App\Models\Company;

class CompanyObserver
{
    public function updated(Company $company)
    {
        if ($company->status !== StatusEnum::ACTIVE && $company->users()->count() > 0) {
            $company->users()->update(['is_active' => false]);
        }
    }
}
