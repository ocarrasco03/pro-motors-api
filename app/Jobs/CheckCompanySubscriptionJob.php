<?php

namespace App\Jobs;

use App\Domain\ValueObjects\Enums\StatusEnum;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckCompanySubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $this->suspendCompaniesWithoutPayment();
        $this->activateCompaniesWithPayment();
    }

    protected function suspendCompaniesWithoutPayment(): void
    {
        $companies = Company::where('status', StatusEnum::ACTIVE)->get();

        foreach ($companies as $company) {
            if ($company->shouldBeSuspended()) {
                $company->update(['status' => StatusEnum::SUSPENDED]);
                $company->users()->update(['status' => StatusEnum::SUSPENDED]);

                Log::info("Company {$company->id} ({$company->name}) has been suspended due to lack of payment.");
            }
        }
    }

    protected function activateCompaniesWithPayment(): void
    {
        $companies = Company::where('status', StatusEnum::SUSPENDED)->get();

        foreach ($companies as $company) {
            if ($company->hasCurrentMonthPayment()) {
                $company->update(['status' => StatusEnum::ACTIVE]);
                $company->users()->update(['status' => StatusEnum::ACTIVE]);

                Log::info("Company {$company->id} ({$company->name}) has been reactivated due to payment.");
            }
        }
    }
}
