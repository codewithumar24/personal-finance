<?php
// modules/User/Database/Seeders/AdminUserSeeder.php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Entities\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $adminUser = User::create([
            'user_uuid' => Str::uuid(),
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@finance.com',
            'password' => Hash::make('admin123'),
            'phone' => '+1234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign Admin Role to the user
        $adminRole = DB::table('roles')->where('name', 'admin')->first();

        if ($adminRole) {
            DB::table('user_roles')->insert([
                'user_id' => $adminUser->id,
                'role_id' => $adminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@finance.com');
        $this->command->info('Password: admin123');
    }
}
