<?php
// modules/Finance/Enum/TransactionType.php

namespace Modules\Admin\Enum;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match($this) {
            self::INCOME => 'Income',
            self::EXPENSE => 'Expense',
            self::TRANSFER => 'Transfer',
        };
    }

    public function isPositive(): bool
    {
        return $this === self::INCOME;
    }

    public function isNegative(): bool
    {
        return $this === self::EXPENSE;
    }

    public function isNeutral(): bool
    {
        return $this === self::TRANSFER;
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}