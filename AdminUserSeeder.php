<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // Student
        $student = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'طالب تجريبي',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $student->syncRoles(['student']);

        $this->command->info('✅ Test users created:');
        $this->command->info('   Admin:   admin@example.com / password');
        $this->command->info('   Student: student@example.com / password');
    }
}
