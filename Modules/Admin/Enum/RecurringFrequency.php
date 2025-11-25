<?php
// modules/Finance/Enum/RecurringFrequency.php

namespace Modules\Admin\Enum;

enum RecurringFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match($this) {
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }

    public function getNextDate(\Carbon\Carbon $fromDate): \Carbon\Carbon
    {
        return match($this) {
            self::DAILY => $fromDate->copy()->addDay(),
            self::WEEKLY => $fromDate->copy()->addWeek(),
            self::MONTHLY => $fromDate->copy()->addMonth(),
            self::YEARLY => $fromDate->copy()->addYear(),
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}