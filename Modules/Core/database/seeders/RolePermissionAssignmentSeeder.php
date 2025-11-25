<?php
// modules/Core/Database/Seeders/RolePermissionAssignmentSeeder.php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all roles
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $managerRole = DB::table('roles')->where('name', 'manager')->first();
        $userRole = DB::table('roles')->where('name', 'user')->first();
        $viewerRole = DB::table('roles')->where('name', 'viewer')->first();

        // Get all permissions
        $allPermissions = DB::table('permissions')->pluck('id');

        // Manager permissions (everything except user/role management)
        $managerPermissions = DB::table('permissions')
            ->whereNotIn('group', ['user_management', 'role_management', 'permission_management', 'system'])
            ->pluck('id');

        // User permissions (basic CRUD operations)
        $userPermissions = DB::table('permissions')
            ->whereIn('group', ['wallet', 'transaction', 'category', 'budget', 'goal', 'report', 'dashboard'])
            ->where('name', 'NOT LIKE', '%.manage')
            ->where('name', 'NOT LIKE', '%.delete')
            ->pluck('id');

        // Viewer permissions (read-only)
        $viewerPermissions = DB::table('permissions')
            ->where('name', 'LIKE', '%.view')
            ->orWhere('name', 'LIKE', 'reports.view')
            ->orWhere('name', 'LIKE', 'dashboard.view')
            ->pluck('id');

        // Assign permissions to roles
        $this->assignPermissionsToRole($superAdminRole->id, $allPermissions);
        $this->assignPermissionsToRole($adminRole->id, $allPermissions->except(
            DB::table('permissions')->where('group', 'system')->pluck('id')->toArray()
        ));
        $this->assignPermissionsToRole($managerRole->id, $managerPermissions);
        $this->assignPermissionsToRole($userRole->id, $userPermissions);
        $this->assignPermissionsToRole($viewerRole->id, $viewerPermissions);

        $this->command->info('Role permissions assigned successfully!');
    }

    private function assignPermissionsToRole(int $roleId, $permissions): void
    {
        $rolePermissions = [];

        foreach ($permissions as $permissionId) {
            $rolePermissions[] = [
                'permission_id' => $permissionId,
                'role_id' => $roleId,
            ];
        }

        DB::table('role_has_permissions')->insert($rolePermissions);
    }
}
