<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Enums;

enum RelationTypeEnum: string
{
    case EQUIVALENT = 'equivalent';
    case SUBSTITUTE = 'substitute';
    case COMPLEMENTARY = 'complementary';
    case ACCESSORY = 'accessory';
    case UPGRADE = 'upgrade';
    case CROSS_REFERENCE = 'cross_reference';
}
