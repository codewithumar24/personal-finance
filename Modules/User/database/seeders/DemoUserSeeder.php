<?php
// modules/User/Database/Seeders/DemoUserSeeder.php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Entities\User;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Demo Regular User
        $demoUser = User::create([
            'user_uuid' => Str::uuid(),
            'first_name' => 'Demo',
            'last_name' => 'User',
            'email' => 'user@finance.com',
            'password' => Hash::make('user123'),
            'phone' => '+1234567891',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign User Role to the demo user
        $userRole = DB::table('roles')->where('name', 'user')->first();

        if ($userRole) {
            DB::table('user_roles')->insert([
                'user_id' => $demoUser->id,
                'role_id' => $userRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Demo user created successfully!');
        $this->command->info('Email: user@finance.com');
        $this->command->info('Password: user123');
    }
}
