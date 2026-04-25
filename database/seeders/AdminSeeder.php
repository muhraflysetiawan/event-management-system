<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@tms.test'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create demo users for testing
        $studentRole = Role::where('slug', 'student')->first();
        $committeeRole = Role::where('slug', 'committee')->first();
        $lecturerRole = Role::where('slug', 'lecturer')->first();
        $headRole = Role::where('slug', 'head_csdl')->first();

        User::firstOrCreate(
            ['email' => 'committee@tms.test'],
            [
                'name' => 'Event Committee',
                'email' => 'committee@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $committeeRole->id ?? null,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'head@tms.test'],
            [
                'name' => 'Head of CSDL',
                'email' => 'head@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $headRole->id ?? null,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'student@tms.test'],
            [
                'name' => 'Demo Student',
                'email' => 'student@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id ?? null,
                'student_id' => 'STD-2024-001',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'lecturer@tms.test'],
            [
                'name' => 'Demo Lecturer',
                'email' => 'lecturer@tms.test',
                'password' => Hash::make('password'),
                'role_id' => $lecturerRole->id ?? null,
                'email_verified_at' => now(),
            ]
        );
    }
}
