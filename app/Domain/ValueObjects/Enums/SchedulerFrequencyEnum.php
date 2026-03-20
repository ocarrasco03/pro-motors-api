<?php

namespace App\Domain\ValueObjects\Enums;

enum SchedulerFrequencyEnum: string
{
    case EVERY_MINUTE = 'every_minute';
    case EVERY_5_MINUTES = 'every_5_minutes';
    case EVERY_15_MINUTES = 'every_15_minutes';
    case EVERY_30_MINUTES = 'every_30_minutes';
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public function getCronExpression(?string $time = null): string
    {
        return match ($this) {
            self::EVERY_MINUTE => '* * * * *',
            self::EVERY_5_MINUTES => '*/5 * * * *',
            self::EVERY_15_MINUTES => '*/15 * * * *',
            self::EVERY_30_MINUTES => '*/30 * * * *',
            self::HOURLY => '0 * * * *',
            self::DAILY => $this->parseDailyTime($time),
            self::WEEKLY => $this->parseWeeklyTime($time),
            self::MONTHLY => $this->parseMonthlyTime($time),
        };
    }

    private function parseDailyTime(?string $time): string
    {
        $defaultTime = '22:00';
        $time = $time ?? $defaultTime;
        [$hour, $minute] = explode(':', $time) + [0, 0];

        return sprintf('%d %d * * *', $minute, $hour);
    }

    private function parseWeeklyTime(?string $time): string
    {
        $defaultTime = '22:00';
        $defaultDay = 'monday';
        $time = $time ?? $defaultTime;
        [$day, $timePart] = explode(':', $time).$defaultTime;
        [$hour, $minute] = explode(':', $timePart) + [0, 0];

        $dayNumber = match (strtolower($day)) {
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            default => 1,
        };

        return sprintf('%d %d * * %d', $minute, $hour, $dayNumber);
    }

    private function parseMonthlyTime(?string $time): string
    {
        $defaultTime = '22:00';
        $defaultDay = '1';
        $time = $time ?? $defaultTime;
        [$day, $timePart] = explode(':', $time).$defaultTime;
        [$hour, $minute] = explode(':', $timePart) + [0, 0];

        return sprintf('%d %d %d * *', $minute, $hour, (int) $day);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::EVERY_MINUTE => 'Cada minuto',
            self::EVERY_5_MINUTES => 'Cada 5 minutos',
            self::EVERY_15_MINUTES => 'Cada 15 minutos',
            self::EVERY_30_MINUTES => 'Cada 30 minutos',
            self::HOURLY => 'Cada hora',
            self::DAILY => 'Diario',
            self::WEEKLY => 'Semanal',
            self::MONTHLY => 'Mensual',
        };
    }

    public static function getOptions(): array
    {
        return [
            ['value' => self::EVERY_MINUTE->value, 'label' => self::EVERY_MINUTE->getLabel()],
            ['value' => self::EVERY_5_MINUTES->value, 'label' => self::EVERY_5_MINUTES->getLabel()],
            ['value' => self::EVERY_15_MINUTES->value, 'label' => self::EVERY_15_MINUTES->getLabel()],
            ['value' => self::EVERY_30_MINUTES->value, 'label' => self::EVERY_30_MINUTES->getLabel()],
            ['value' => self::HOURLY->value, 'label' => self::HOURLY->getLabel()],
            ['value' => self::DAILY->value, 'label' => self::DAILY->getLabel()],
            ['value' => self::WEEKLY->value, 'label' => self::WEEKLY->getLabel()],
            ['value' => self::MONTHLY->value, 'label' => self::MONTHLY->getLabel()],
        ];
    }
}
