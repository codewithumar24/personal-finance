<?php
// modules/User/Database/Seeders/SuperAdminSeeder.php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Entities\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::create([
            'user_uuid' => Str::uuid(),
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'superadmin@finance.com',
            'password' => Hash::make('superadmin123'),
            'phone' => '+1234567899',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign Super Admin Role
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();

        if ($superAdminRole) {
            DB::table('user_roles')->insert([
                'user_id' => $superAdmin->id,
                'role_id' => $superAdminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('🎉 Super Admin user created successfully!');
        $this->command->info('📧 Email: superadmin@finance.com');
        $this->command->info('🔑 Password: superadmin123');
        $this->command->info('👑 Role: Super Administrator (Full system access)');
    }
}
