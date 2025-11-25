<?php
// modules/Core/Database/Seeders/RoleSeeder.php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'role_uuid' => Str::uuid(),
                'name' => 'super_admin',
                'guard_name' => 'api',
                'description' => 'Super Administrator - Full system access',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_uuid' => Str::uuid(),
                'name' => 'admin',
                'guard_name' => 'api',
                'description' => 'Administrator - System management',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_uuid' => Str::uuid(),
                'name' => 'manager',
                'guard_name' => 'api',
                'description' => 'Manager - Team management capabilities',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_uuid' => Str::uuid(),
                'name' => 'user',
                'guard_name' => 'api',
                'description' => 'Regular User - Basic access',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_uuid' => Str::uuid(),
                'name' => 'viewer',
                'guard_name' => 'api',
                'description' => 'Viewer - Read-only access',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);

        $this->command->info('Roles seeded successfully!');
    }
}
