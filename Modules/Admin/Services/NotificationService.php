<?php
// modules/Finance/Services/NotificationService.php

namespace Modules\Admin\Services;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Contracts\Services\NotificationServiceContract;
use Modules\Admin\Entities\UserNotificationSetting;
use Modules\Admin\Enum\NotificationType;
use Modules\Admin\Notifications\BudgetExceededNotification;
use Modules\Admin\Notifications\GoalMilestoneNotification;
use Modules\Admin\Notifications\WeeklySummaryNotification;
use Modules\User\Entities\User;

class NotificationService implements NotificationServiceContract
{
    public function sendBudgetNotification(string $userId, string $type, array $data): void
    {
        $user = User::find($userId);
        if (!$user) return;

        $notificationType = NotificationType::from($type);
        $settings = $this->getUserNotificationSettings($userId, $notificationType);

        if (empty($settings['enabled_channels'])) return;

        $notification = match($notificationType) {
            NotificationType::BUDGET_EXCEEDED => new BudgetExceededNotification($data),
            // Add other budget notification types here
            default => null,
        };

        if ($notification) {
            $user->notify($notification);
        }
    }

    public function sendGoalNotification(string $userId, string $type, array $data): void
    {
        $user = User::find($userId);
        if (!$user) return;

        $notificationType = NotificationType::from($type);
        $settings = $this->getUserNotificationSettings($userId, $notificationType);

        if (empty($settings['enabled_channels'])) return;

        $notification = match($notificationType) {
            NotificationType::GOAL_MILESTONE => new GoalMilestoneNotification($data),
            // Add other goal notification types here
            default => null,
        };

        if ($notification) {
            $user->notify($notification);
        }
    }

    public function sendTransactionNotification(string $userId, string $type, array $data): void
    {
        // Implementation for transaction notifications
        $user = User::find($userId);
        if (!$user) return;

        $notificationType = NotificationType::from($type);
        $settings = $this->getUserNotificationSettings($userId, $notificationType);

        if (empty($settings['enabled_channels'])) return;

        // Create and send transaction notification
    }

    public function sendWalletNotification(string $userId, string $type, array $data): void
    {
        // Implementation for wallet notifications
        $user = User::find($userId);
        if (!$user) return;

        $notificationType = NotificationType::from($type);
        $settings = $this->getUserNotificationSettings($userId, $notificationType);

        if (empty($settings['enabled_channels'])) return;

        // Create and send wallet notification
    }

    public function sendSystemNotification(string $userId, string $type, array $data): void
    {
        $user = User::find($userId);
        if (!$user) return;

        $notificationType = NotificationType::from($type);
        $settings = $this->getUserNotificationSettings($userId, $notificationType);

        if (empty($settings['enabled_channels'])) return;

        $notification = match($notificationType) {
            NotificationType::WEEKLY_SUMMARY => new WeeklySummaryNotification($data),
            // Add other system notification types here
            default => null,
        };

        if ($notification) {
            $user->notify($notification);
        }
    }

    public function getUserNotifications(string $userId, array $filters = [], int $perPage = 15): array
    {
        $query = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->orderBy('created_at', 'desc');

        if (!empty($filters['type'])) {
            $query->where('type', 'like', '%' . $filters['type'] . '%');
        }

        if (!empty($filters['unread_only'])) {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate($perPage);

        return [
            'notifications' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'last_page' => $notifications->lastPage(),
            ],
            'unread_count' => DB::table('notifications')
                ->where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->whereNull('read_at')
                ->count(),
        ];
    }

    public function markAsRead(string $userId, string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead(string $userId): void
    {
        DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

//    public function getUserNotificationSettings(string $userId): array
//    {
//        $settings = UserNotificationSetting::where('user_id', $userId)->get();
//
//        $defaultSettings = collect(NotificationType::cases())->mapWithKeys(function ($type) {
//            return [
//                $type->value => [
//                    'type' => $type->value,
//                    'category' => $type->category(),
//                    'email_enabled' => in_array('email', $type->defaultChannels()),
//                    'push_enabled' => in_array('push', $type->defaultChannels()),
//                    'in_app_enabled' => in_array('in_app', $type->defaultChannels()),
//                    'is_critical' => $type->isCritical(),
//                ]
//            ];
//        });
//
//        // Merge with user's custom settings
//        foreach ($settings as $userSetting) {
//            if (isset($defaultSettings[$userSetting->type])) {
//                $defaultSettings[$userSetting->type] = array_merge(
//                    $defaultSettings[$userSetting->type],
//                    $userSetting->only(['email_enabled', 'push_enabled', 'in_app_enabled', 'channels'])
//                );
//            }
//        }
//
//        return $defaultSettings->values()->toArray();
//    }

    public function updateNotificationSettings(string $userId, array $settings): void
    {
        DB::transaction(function () use ($userId, $settings) {
            foreach ($settings as $type => $config) {
                UserNotificationSetting::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'type' => $type,
                    ],
                    [
                        'email_enabled' => $config['email_enabled'] ?? true,
                        'push_enabled' => $config['push_enabled'] ?? true,
                        'in_app_enabled' => $config['in_app_enabled'] ?? true,
                        'channels' => $config['channels'] ?? null,
                    ]
                );
            }
        });
    }

    public function getUserNotificationSettings(string $userId, NotificationType $type): array
    {
        $setting = UserNotificationSetting::where('user_id', $userId)
            ->where('type', $type->value)
            ->first();

        if ($setting) {
            return [
                'enabled_channels' => $setting->getEnabledChannels(),
                'settings' => $setting->toArray(),
            ];
        }

        // Return default channels if no custom settings
        return [
            'enabled_channels' => $type->defaultChannels(),
            'settings' => null,
        ];
    }

    // Add this method to the existing BudgetService
public function checkAndSendBudgetAlerts(string $userId): array
{
    $alerts = $this->checkBudgetAlerts($userId);

    foreach ($alerts as $alert) {
        $this->notificationService->sendBudgetNotification(
            $userId,
            $alert['type'],
            [
                'category_name' => $alert['message'],
                'budget_amount' => $alert['budget']->amount ?? 0,
                'spent_amount' => $alert['budget']->spent_amount ?? 0,
                'overspent_amount' => $alert['budget']->spent_amount - $alert['budget']->amount ?? 0,
                'budget_id' => $alert['budget_id'],
            ]
        );
    }

    return $alerts;
}

}
