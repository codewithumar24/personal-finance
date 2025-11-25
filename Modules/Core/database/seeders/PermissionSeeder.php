<?php
// modules/Core/Database/Seeders/PermissionSeeder.php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'users.view', 'guard_name' => 'api', 'group' => 'user_management', 'description' => 'View users'],
            ['permission_uuid' => Str::uuid(), 'name' => 'users.create', 'guard_name' => 'api', 'group' => 'user_management', 'description' => 'Create users'],
            ['permission_uuid' => Str::uuid(), 'name' => 'users.update', 'guard_name' => 'api', 'group' => 'user_management', 'description' => 'Update users'],
            ['permission_uuid' => Str::uuid(), 'name' => 'users.delete', 'guard_name' => 'api', 'group' => 'user_management', 'description' => 'Delete users'],
            ['permission_uuid' => Str::uuid(), 'name' => 'users.activate', 'guard_name' => 'api', 'group' => 'user_management', 'description' => 'Activate/deactivate users'],

            // Role Management Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'roles.view', 'guard_name' => 'api', 'group' => 'role_management', 'description' => 'View roles'],
            ['permission_uuid' => Str::uuid(), 'name' => 'roles.create', 'guard_name' => 'api', 'group' => 'role_management', 'description' => 'Create roles'],
            ['permission_uuid' => Str::uuid(), 'name' => 'roles.update', 'guard_name' => 'api', 'group' => 'role_management', 'description' => 'Update roles'],
            ['permission_uuid' => Str::uuid(), 'name' => 'roles.delete', 'guard_name' => 'api', 'group' => 'role_management', 'description' => 'Delete roles'],
            ['permission_uuid' => Str::uuid(), 'name' => 'roles.assign', 'guard_name' => 'api', 'group' => 'role_management', 'description' => 'Assign roles to users'],

            // Permission Management
            ['permission_uuid' => Str::uuid(), 'name' => 'permissions.view', 'guard_name' => 'api', 'group' => 'permission_management', 'description' => 'View permissions'],
            ['permission_uuid' => Str::uuid(), 'name' => 'permissions.assign', 'guard_name' => 'api', 'group' => 'permission_management', 'description' => 'Assign permissions to roles'],

            // Wallet Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'wallets.view', 'guard_name' => 'api', 'group' => 'wallet', 'description' => 'View wallets'],
            ['permission_uuid' => Str::uuid(), 'name' => 'wallets.create', 'guard_name' => 'api', 'group' => 'wallet', 'description' => 'Create wallets'],
            ['permission_uuid' => Str::uuid(), 'name' => 'wallets.update', 'guard_name' => 'api', 'group' => 'wallet', 'description' => 'Update wallets'],
            ['permission_uuid' => Str::uuid(), 'name' => 'wallets.delete', 'guard_name' => 'api', 'group' => 'wallet', 'description' => 'Delete wallets'],
            ['permission_uuid' => Str::uuid(), 'name' => 'wallets.manage', 'guard_name' => 'api', 'group' => 'wallet', 'description' => 'Manage all wallets'],

            // Transaction Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'transactions.view', 'guard_name' => 'api', 'group' => 'transaction', 'description' => 'View transactions'],
            ['permission_uuid' => Str::uuid(), 'name' => 'transactions.create', 'guard_name' => 'api', 'group' => 'transaction', 'description' => 'Create transactions'],
            ['permission_uuid' => Str::uuid(), 'name' => 'transactions.update', 'guard_name' => 'api', 'group' => 'transaction', 'description' => 'Update transactions'],
            ['permission_uuid' => Str::uuid(), 'name' => 'transactions.delete', 'guard_name' => 'api', 'group' => 'transaction', 'description' => 'Delete transactions'],
            ['permission_uuid' => Str::uuid(), 'name' => 'transactions.manage', 'guard_name' => 'api', 'group' => 'transaction', 'description' => 'Manage all transactions'],

            // Category Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'categories.view', 'guard_name' => 'api', 'group' => 'category', 'description' => 'View categories'],
            ['permission_uuid' => Str::uuid(), 'name' => 'categories.create', 'guard_name' => 'api', 'group' => 'category', 'description' => 'Create categories'],
            ['permission_uuid' => Str::uuid(), 'name' => 'categories.update', 'guard_name' => 'api', 'group' => 'category', 'description' => 'Update categories'],
            ['permission_uuid' => Str::uuid(), 'name' => 'categories.delete', 'guard_name' => 'api', 'group' => 'category', 'description' => 'Delete categories'],

            // Budget Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'budgets.view', 'guard_name' => 'api', 'group' => 'budget', 'description' => 'View budgets'],
            ['permission_uuid' => Str::uuid(), 'name' => 'budgets.create', 'guard_name' => 'api', 'group' => 'budget', 'description' => 'Create budgets'],
            ['permission_uuid' => Str::uuid(), 'name' => 'budgets.update', 'guard_name' => 'api', 'group' => 'budget', 'description' => 'Update budgets'],
            ['permission_uuid' => Str::uuid(), 'name' => 'budgets.delete', 'guard_name' => 'api', 'group' => 'budget', 'description' => 'Delete budgets'],

            // Goal Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'goals.view', 'guard_name' => 'api', 'group' => 'goal', 'description' => 'View goals'],
            ['permission_uuid' => Str::uuid(), 'name' => 'goals.create', 'guard_name' => 'api', 'group' => 'goal', 'description' => 'Create goals'],
            ['permission_uuid' => Str::uuid(), 'name' => 'goals.update', 'guard_name' => 'api', 'group' => 'goal', 'description' => 'Update goals'],
            ['permission_uuid' => Str::uuid(), 'name' => 'goals.delete', 'guard_name' => 'api', 'group' => 'goal', 'description' => 'Delete goals'],

            // Report Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'reports.view', 'guard_name' => 'api', 'group' => 'report', 'description' => 'View reports'],
            ['permission_uuid' => Str::uuid(), 'name' => 'reports.export', 'guard_name' => 'api', 'group' => 'report', 'description' => 'Export reports'],
            ['permission_uuid' => Str::uuid(), 'name' => 'reports.analytics', 'guard_name' => 'api', 'group' => 'report', 'description' => 'Access analytics'],

            // System Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'system.settings', 'guard_name' => 'api', 'group' => 'system', 'description' => 'Manage system settings'],
            ['permission_uuid' => Str::uuid(), 'name' => 'system.backup', 'guard_name' => 'api', 'group' => 'system', 'description' => 'Perform system backups'],
            ['permission_uuid' => Str::uuid(), 'name' => 'system.monitor', 'guard_name' => 'api', 'group' => 'system', 'description' => 'Monitor system health'],

            // Dashboard Permissions
            ['permission_uuid' => Str::uuid(), 'name' => 'dashboard.view', 'guard_name' => 'api', 'group' => 'dashboard', 'description' => 'View dashboard'],
            ['permission_uuid' => Str::uuid(), 'name' => 'dashboard.analytics', 'guard_name' => 'api', 'group' => 'dashboard', 'description' => 'View analytics dashboard'],
        ];

        DB::table('permissions')->insert($permissions);

        $this->command->info('Permissions seeded successfully!');
    }
}
