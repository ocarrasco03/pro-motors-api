<?php

namespace App\Core\Enums;

enum BillingPeriodEnum: string
{
    case MONTHLY = 'monthly';
    case BIMONTHLY = 'bimonthly';
    case QUARTERLY = 'quarterly';
    case BIANNUAL = 'biannual';
    case ANNUAL = 'annual';

    public function label(): string
    {
        return match ($this) {
            static::MONTHLY => trans('enums.billing_period.monthly'),
            static::BIMONTHLY => trans('enums.billing_period.bimonthly'),
            static::QUARTERLY => trans('enums.billing_period.quarterly'),
            static::BIANNUAL => trans('enums.billing_period.biannual'),
            static::ANNUAL => trans('enums.billing_period.annual'),
        };
    }
}
