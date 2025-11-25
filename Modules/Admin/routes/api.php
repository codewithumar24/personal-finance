<?php
// modules/Admin/Routes/api.php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminDashboardController;
use Modules\Admin\Http\Controllers\AdminUserController;
use Modules\Admin\Http\Controllers\AdminRoleController;
use Modules\Admin\Http\Controllers\BudgetController;
use Modules\Admin\Http\Controllers\CategoryController;
use Modules\Admin\Http\Controllers\GoalController;
use Modules\Admin\Http\Controllers\NotificationController;
use Modules\Admin\Http\Controllers\ReportController;
use Modules\Admin\Http\Controllers\TransactionController;
use Modules\Admin\Http\Controllers\WalletController;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [AdminDashboardController::class, 'stats']);
        Route::get('recent-activity', [AdminDashboardController::class, 'recentActivity']);
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index']);
        Route::post('/', [AdminUserController::class, 'store']);
        Route::get('{uuid}', [AdminUserController::class, 'show']);
        Route::put('{uuid}', [AdminUserController::class, 'update']);
        Route::delete('{uuid}', [AdminUserController::class, 'destroy']);
        Route::post('{uuid}/assign-roles', [AdminUserController::class, 'assignRoles']);
        Route::post('{uuid}/activate', [AdminUserController::class, 'activate']);
        Route::post('{uuid}/deactivate', [AdminUserController::class, 'deactivate']);
    });

    // Role Management
    Route::prefix('roles')->group(function () {
        Route::get('/', [AdminRoleController::class, 'index']);
        Route::post('/', [AdminRoleController::class, 'store']);
        Route::get('{uuid}', [AdminRoleController::class, 'show']);
        Route::put('{uuid}', [AdminRoleController::class, 'update']);
        Route::delete('{uuid}', [AdminRoleController::class, 'destroy']);
        Route::get('{uuid}/users', [AdminRoleController::class, 'roleUsers']);
        Route::post('{uuid}/permissions/{permission}', [AdminRoleController::class, 'assignPermission']);
        Route::delete('{uuid}/permissions/{permission}', [AdminRoleController::class, 'removePermission']);
    });
});

Route::middleware(['auth:sanctum'])->prefix('finance')->group(function () {
    // Wallet Routes
    Route::prefix('wallets')->group(function () {
        Route::get('/', [WalletController::class, 'index']);
        Route::post('/', [WalletController::class, 'store']);
        Route::get('total-balance', [WalletController::class, 'totalBalance']);
        Route::get('{uuid}', [WalletController::class, 'show']);
        Route::put('{uuid}', [WalletController::class, 'update']);
        Route::delete('{uuid}', [WalletController::class, 'destroy']);
        Route::get('{uuid}/stats', [WalletController::class, 'stats']);
    });

    // Category Routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('types', [CategoryController::class, 'types']);
        Route::get('type/{type}', [CategoryController::class, 'byType']);
        Route::get('{uuid}', [CategoryController::class, 'show']);
        Route::put('{uuid}', [CategoryController::class, 'update']);
        Route::delete('{uuid}', [CategoryController::class, 'destroy']);
    });


     // Transaction Routes
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/', [TransactionController::class, 'store']);
        Route::get('stats', [TransactionController::class, 'stats']);
        Route::get('{uuid}', [TransactionController::class, 'show']);
        Route::put('{uuid}', [TransactionController::class, 'update']);
        Route::delete('{uuid}', [TransactionController::class, 'destroy']);

        // Filtered transactions
        Route::get('wallet/{walletUuid}', [TransactionController::class, 'walletTransactions']);
        Route::get('category/{categoryUuid}', [TransactionController::class, 'categoryTransactions']);
    });

    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('periods', [ReportController::class, 'reportPeriods']);
        Route::get('income-vs-expense/{period}', [ReportController::class, 'incomeVsExpense']);
        Route::get('category-spending/{period}', [ReportController::class, 'categorySpending']);
        Route::get('wallet-summary/{period}', [ReportController::class, 'walletSummary']);
        Route::get('monthly-summary/{year}', [ReportController::class, 'monthlySummary']);
        Route::get('yearly-summary/{startYear}/{endYear}', [ReportController::class, 'yearlySummary']);
        Route::get('trend-analysis/{type}/{period}', [ReportController::class, 'trendAnalysis']);
        Route::get('top-categories/{period}', [ReportController::class, 'topCategories']);
        Route::get('cash-flow', [ReportController::class, 'cashFlow']);
        Route::post('pdf', [ReportController::class, 'pdfReport']);
    });

    // Budget Routes
    Route::prefix('budgets')->group(function () {
        Route::get('/', [BudgetController::class, 'index']);
        Route::post('/', [BudgetController::class, 'store']);
        Route::get('current', [BudgetController::class, 'current']);
        Route::get('alerts', [BudgetController::class, 'alerts']);
        Route::get('{uuid}', [BudgetController::class, 'show']);
        Route::put('{uuid}', [BudgetController::class, 'update']);
        Route::delete('{uuid}', [BudgetController::class, 'destroy']);
        Route::get('{uuid}/progress', [BudgetController::class, 'progress']);
    });

    // Goal Routes
    Route::prefix('goals')->group(function () {
        Route::get('/', [GoalController::class, 'index']);
        Route::post('/', [GoalController::class, 'store']);
        Route::get('alerts', [GoalController::class, 'alerts']);
        Route::get('{uuid}', [GoalController::class, 'show']);
        Route::put('{uuid}', [GoalController::class, 'update']);
        Route::delete('{uuid}', [GoalController::class, 'destroy']);
        Route::post('{uuid}/add-amount', [GoalController::class, 'addAmount']);
        Route::get('{uuid}/progress', [GoalController::class, 'progress']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::get('settings', [NotificationController::class, 'settings']);
        Route::put('settings', [NotificationController::class, 'updateSettings']);
    });
});
