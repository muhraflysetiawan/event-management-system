<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Committee', 'slug' => 'committee'],
            ['name' => 'Student', 'slug' => 'student'],
            ['name' => 'Lecturer', 'slug' => 'lecturer'],
            ['name' => 'Staff', 'slug' => 'staff'],
            ['name' => 'External', 'slug' => 'external'],
            ['name' => 'Head CSDL', 'slug' => 'head_csdl'],
            ['name' => 'Head BAAK', 'slug' => 'head_baak'],
            ['name' => 'Head Finance', 'slug' => 'head_finance'],
            ['name' => 'Head GSD', 'slug' => 'head_gsd'],
            ['name' => 'Head SIS', 'slug' => 'head_sis'],
            ['name' => 'Head Learning', 'slug' => 'head_learning'],
            ['name' => 'ACOO', 'slug' => 'acoo'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
