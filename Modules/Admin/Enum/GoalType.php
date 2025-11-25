<?php
// modules/Finance/Enum/GoalType.php

namespace Modules\Admin\Enum;

enum GoalType: string
{
    case SAVINGS = 'savings';
    case DEBT = 'debt';
    case INVESTMENT = 'investment';
    case PURCHASE = 'purchase';

    public function label(): string
    {
        return match($this) {
            self::SAVINGS => 'Savings',
            self::DEBT => 'Debt Payment',
            self::INVESTMENT => 'Investment',
            self::PURCHASE => 'Purchase',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
