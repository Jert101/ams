<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin role ID
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();

        if ($adminRole) {
            // Create admin user
            $admin = \App\Models\User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]);

            // Create QR code for admin
            \App\Models\QrCode::create([
                'user_id' => $admin->id,
                'code' => \App\Models\QrCode::generateUniqueCode(),
                'is_active' => true,
            ]);
        }
    }
}
