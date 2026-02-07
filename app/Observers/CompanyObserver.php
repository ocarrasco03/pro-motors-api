<?php

namespace App\Observers;

use App\Models\Company;

class CompanyObserver
{
    public function updated(Company $company)
    {
        if (! $company->is_active) {
            $company->users()->update(['is_active' => false]);
        }
    }
}
