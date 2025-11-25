<?php
// modules/Admin/Http/Controllers/AdminDashboardController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Admin\Contracts\Services\AdminUserServiceContract;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly AdminUserServiceContract $adminUserService
    ) {}

    public function stats(): JsonResponse
    {
        $stats = $this->adminUserService->getDashboardStats();

        return apiResponse()->success($stats);
    }

    public function recentActivity(): JsonResponse
    {
        // Get recent users
        $recentUsers = \Modules\User\Entities\User::with(['roles'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get system logs (you can implement a logging system)
        $recentLogs = []; // Implement based on your logging system

        return apiResponse()->success([
            'recent_users' => \Modules\User\Transformers\UserTransformer::collection($recentUsers),
            'recent_activity' => $recentLogs,
        ]);
    }
}