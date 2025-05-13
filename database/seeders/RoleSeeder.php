<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'System administrator with full access to all features',
            ],
            [
                'name' => 'Officer',
                'description' => 'Can approve attendance of members using the QR scanner',
            ],
            [
                'name' => 'Secretary',
                'description' => 'Can create attendance summaries and manage notifications for absent members',
            ],
            [
                'name' => 'Member',
                'description' => 'Regular member with access to personal attendance records',
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
