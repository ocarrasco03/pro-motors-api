<?php

namespace App\Listeners;

use App\Events\SubscriptionExpiring;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSubscriptionWarningListener
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
    public function handle(SubscriptionExpiring $event): void
    {
        $event->subscription->company()
            ->adminUsers()
            ->notify(
                new SubscriptionExpiringNotification($event->subscription)
            );
    }
}
