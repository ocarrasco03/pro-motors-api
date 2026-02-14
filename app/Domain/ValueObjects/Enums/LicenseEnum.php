<?php

namespace App\Domain\ValueObjects\Enums;

enum LicenseEnum: string
{
    case INDIVIDUAL = 'individual';
    case CORPORATE = 'corporate';
}
