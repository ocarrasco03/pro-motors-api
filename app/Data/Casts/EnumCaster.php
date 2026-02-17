<?php

namespace App\Data\Casts;

use BackedEnum;
use InvalidArgumentException;

final class EnumCaster
{
    public static function cast(string $enumClass, mixed $value, ?BackedEnum $default = null): ?BackedEnum
    {
        if ($value === null) {
            return $default;
        }

        if ($value instanceof $enumClass) {
            return $value;
        }

        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("Class {$enumClass} is not an enum.");
        }

        if (!is_subclass_of($enumClass, BackedEnum::class)) {
            throw new InvalidArgumentException("{$enumClass} must be a BackedEnum.");
        }

        $enum = $enumClass::tryFrom($value);

        if (!$enum) {
            throw new InvalidArgumentException("Invalid value [{$value}] for enum {$enumClass}.");
        }

        return $enum;
    }
}
