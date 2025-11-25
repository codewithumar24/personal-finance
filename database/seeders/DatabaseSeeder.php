<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\PermissionSeeder;
use Modules\Core\Database\Seeders\RolePermissionAssignmentSeeder;
use Modules\Core\Database\Seeders\RoleSeeder;
use Modules\User\Database\Seeders\AdminUserSeeder;
use Modules\User\Database\Seeders\DemoUserSeeder;
use Modules\User\Database\Seeders\SuperAdminSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core Module
            RoleSeeder::class,
           PermissionSeeder::class,
            RolePermissionAssignmentSeeder::class,


            // User Module
            SuperAdminSeeder::class,
            AdminUserSeeder::class,
            DemoUserSeeder::class,
        ]);

    }
}
