<?php
// modules/Finance/Enum/CategoryType.php

namespace Modules\Admin\Enum;

enum CategoryType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';
    case SAVINGS = 'savings';

    public function label(): string
    {
        return match($this) {
            self::INCOME => 'Income',
            self::EXPENSE => 'Expense',
            self::TRANSFER => 'Transfer',
            self::SAVINGS => 'Savings',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}