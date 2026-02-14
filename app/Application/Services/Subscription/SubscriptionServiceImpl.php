<?php

namespace App\Application\Services\Subscriptions;

use App\Domain\ValueObjects\Enums\StatusEnum;
use App\Events\SubscriptionExpired;
use App\Events\SubscriptionExpiring;
use App\Models\Payment;
use App\Models\Subscription;

class SubscriptionServiceImpl implements SubscriptionService
{
    public function validateAll(): void
    {
        Subscription::query()
            ->with('company')
            ->where('status', StatusEnum::ACTIVE)
            ->get()
            ->each(fn ($subscription) => $this->validate($subscription));
    }

    protected function validate(Subscription $subscription): void
    {
        if ($this->isPaid($subscription)) {
            return;
        }

        if ($this->isExpired($subscription)) {
            event(new SubscriptionExpired($subscription));
            return;
        }

        if ($this->isExpiringSoon($subscription)) {
            event(new SubscriptionExpiring($subscription));
        }
    }

    protected function isPaid(Subscription $subscription): bool
    {
        return Payment::query()
            ->where('company_id', $subscription->company_id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->where('status', StatusEnum::PAID)
            ->exists();
    }

    protected function isExpired(Subscription $subscription): bool
    {
        return now()->greaterThan(
            $subscription->current_period_end->addDays($subscription->grace_days)
        );
    }

    protected function isExpiringSoon(Subscription $subscription): bool
    {
        return now()->diffInDays(
            $subscription->current_period_end,
            false) <= config('subscriptions.expiration_days', 5);
    }
}
