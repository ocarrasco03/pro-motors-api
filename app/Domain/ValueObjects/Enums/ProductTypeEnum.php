<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects\Enums;

enum ProductTypeEnum: string
{
    case PART = 'part';
    case ACCESSORY = 'accessory';
    case CONSUMABLE = 'consumable';
    case TOOL = 'tool';
    case FLUID = 'fluid';
    case ELECTRICAL = 'electrical';
    case BODY = 'body';
    case ENGINE = 'engine';
    case TRANSMISSION = 'transmission';
    case BRAKE = 'brake';
    case SUSPENSION = 'suspension';
    case FILTRATION = 'filtration';
    case IGNITION = 'ignition';
    case COOLING = 'cooling';
    case EXHAUST = 'exhaust';
    case STEERING = 'steering';
    case OTHER = 'other';
}
