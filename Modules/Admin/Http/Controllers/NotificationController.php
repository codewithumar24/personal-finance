<?php
// modules/Finance/Http/Controllers/NotificationController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Contracts\Services\NotificationServiceContract;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationServiceContract $notificationService
    ) {}

    public function index(): JsonResponse
    {
        $userId = Auth::id();
        $filters = request()->only(['type', 'unread_only']);
        $perPage = request('per_page', 15);

        $notifications = $this->notificationService->getUserNotifications($userId, $filters, $perPage);

        return apiResponse()->success($notifications);
    }

    public function markAsRead(string $id): JsonResponse
    {
        $userId = Auth::id();

        try {
            $this->notificationService->markAsRead($userId, $id);
            return apiResponse()->success(null, 'Notification marked as read');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function markAllAsRead(): JsonResponse
    {
        $userId = Auth::id();

        try {
            $this->notificationService->markAllAsRead($userId);
            return apiResponse()->success(null, 'All notifications marked as read');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function settings(): JsonResponse
    {
        $userId = Auth::id();
        $settings = $this->notificationService->getUserNotificationSettings($userId);

        return apiResponse()->success($settings);
    }

    public function updateSettings(): JsonResponse
    {
        $userId = Auth::id();
        $settings = request()->validate([
            'settings' => 'required|array',
            'settings.*.type' => 'required|string',
            'settings.*.email_enabled' => 'boolean',
            'settings.*.push_enabled' => 'boolean',
            'settings.*.in_app_enabled' => 'boolean',
        ]);

        try {
            $this->notificationService->updateNotificationSettings($userId, $settings['settings']);
            return apiResponse()->success(null, 'Notification settings updated successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function unreadCount(): JsonResponse
    {
        $userId = Auth::id();
        $notifications = $this->notificationService->getUserNotifications($userId, [], 1);

        return apiResponse()->success([
            'unread_count' => $notifications['unread_count'] ?? 0
        ]);
    }
}
