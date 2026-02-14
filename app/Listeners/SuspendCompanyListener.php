<?php

namespace App\Listeners;

use App\Domain\ValueObjects\Enums\StatusEnum;
use App\Events\SubscriptionExpired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuspendCompanyListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SubscriptionExpired $event): void
    {
        $event->subscription->company->update([
            'status' => StatusEnum::SUSPENDED,
        ]);
    }
}
