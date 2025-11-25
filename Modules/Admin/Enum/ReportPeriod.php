<?php
// modules/Finance/Enum/ReportPeriod.php

namespace Modules\Admin\Enum;

enum ReportPeriod: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match($this) {
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::YEARLY => 'Yearly',
            self::CUSTOM => 'Custom',
        };
    }

    public function getDateRange(string $date = null): array
    {
        $date = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();

        return match($this) {
            self::DAILY => [
                'start' => $date->copy()->startOfDay(),
                'end' => $date->copy()->endOfDay(),
            ],
            self::WEEKLY => [
                'start' => $date->copy()->startOfWeek(),
                'end' => $date->copy()->endOfWeek(),
            ],
            self::MONTHLY => [
                'start' => $date->copy()->startOfMonth(),
                'end' => $date->copy()->endOfMonth(),
            ],
            self::QUARTERLY => [
                'start' => $date->copy()->startOfQuarter(),
                'end' => $date->copy()->endOfQuarter(),
            ],
            self::YEARLY => [
                'start' => $date->copy()->startOfYear(),
                'end' => $date->copy()->endOfYear(),
            ],
            self::CUSTOM => [
                'start' => $date->copy()->startOfDay(),
                'end' => $date->copy()->endOfDay(),
            ],
        };
    }
}
