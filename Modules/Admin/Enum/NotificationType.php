<?php
// modules/Finance/Enum/NotificationType.php

namespace Modules\Admin\Enum;

enum NotificationType: string
{
    // Budget Notifications
    case BUDGET_EXCEEDED = 'budget_exceeded';
    case BUDGET_WARNING = 'budget_warning';
    case BUDGET_TIME_WARNING = 'budget_time_warning';

    // Goal Notifications
    case GOAL_MILESTONE = 'goal_milestone';
    case GOAL_BEHIND = 'goal_behind';
    case GOAL_DEADLINE = 'goal_deadline';
    case GOAL_COMPLETED = 'goal_completed';

    // Transaction Notifications
    case LARGE_TRANSACTION = 'large_transaction';
    case RECURRING_TRANSACTION_CREATED = 'recurring_transaction_created';

    // Wallet Notifications
    case LOW_BALANCE = 'low_balance';
    case WALLET_LIMIT = 'wallet_limit';

    // System Notifications
    case WEEKLY_SUMMARY = 'weekly_summary';
    case MONTHLY_REPORT = 'monthly_report';
    case SECURITY_ALERT = 'security_alert';

    public function category(): string
    {
        return match($this) {
            self::BUDGET_EXCEEDED,
            self::BUDGET_WARNING,
            self::BUDGET_TIME_WARNING => 'budget',

            self::GOAL_MILESTONE,
            self::GOAL_BEHIND,
            self::GOAL_DEADLINE,
            self::GOAL_COMPLETED => 'goal',

            self::LARGE_TRANSACTION,
            self::RECURRING_TRANSACTION_CREATED => 'transaction',

            self::LOW_BALANCE,
            self::WALLET_LIMIT => 'wallet',

            self::WEEKLY_SUMMARY,
            self::MONTHLY_REPORT,
            self::SECURITY_ALERT => 'system',
        };
    }

    public function defaultChannels(): array
    {
        return match($this) {
            self::BUDGET_EXCEEDED,
            self::LOW_BALANCE,
            self::SECURITY_ALERT => ['email', 'in_app'],

            self::WEEKLY_SUMMARY,
            self::MONTHLY_REPORT => ['email'],

            default => ['in_app'],
        };
    }

    public function isCritical(): bool
    {
        return match($this) {
            self::BUDGET_EXCEEDED,
            self::LOW_BALANCE,
            self::SECURITY_ALERT => true,
            default => false,
        };
    }
}
