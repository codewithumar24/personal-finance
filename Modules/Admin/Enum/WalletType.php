<?php
// modules/Finance/Enum/WalletType.php

namespace Modules\Admin\Enum;

enum WalletType: string
{
    case CASH = 'cash';
    case BANK = 'bank';
    case DIGITAL_WALLET = 'digital_wallet';
    case CREDIT_CARD = 'credit_card';
    case SAVINGS = 'savings';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'Cash',
            self::BANK => 'Bank Account',
            self::DIGITAL_WALLET => 'Digital Wallet',
            self::CREDIT_CARD => 'Credit Card',
            self::SAVINGS => 'Savings Account',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}