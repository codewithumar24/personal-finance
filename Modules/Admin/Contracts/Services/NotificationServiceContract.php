<?php
namespace Modules\Admin\Contracts\Services;

use Modules\Admin\Enum\NotificationType;

interface NotificationServiceContract
{
    public function sendBudgetNotification(string $userId, string $type, array $data): void;
    public function sendGoalNotification(string $userId, string $type, array $data): void;
    public function sendTransactionNotification(string $userId, string $type, array $data): void;
    public function sendWalletNotification(string $userId, string $type, array $data): void;
    public function sendSystemNotification(string $userId, string $type, array $data): void;
    public function getUserNotifications(string $userId, array $filters = [], int $perPage = 15): array;
    public function markAsRead(string $userId, string $notificationId): void;
    public function markAllAsRead(string $userId): void;
    public function getUserNotificationSettings(string $userId,NotificationType $type): array;
    public function updateNotificationSettings(string $userId, array $settings): void;
}
