<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Enums;

enum CurrencyEnum: string
{
    case MXN = 'MXN';
    case USD = 'USD';
    case EUR = 'EUR';
}
