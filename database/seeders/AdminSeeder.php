<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure the roles exist
        $adminRole = Role::where('name', 'Admin')->first();
        
        if (!$adminRole) {
            $this->call(RoleSeeder::class);
            $adminRole = Role::where('name', 'Admin')->first();
        }
        
        // Create an admin user
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
                'user_id' => 110001,
                'profile_photo_path' => 'kofa.png',
                'mobile_number' => '0987654321',
                'approval_status' => 'approved',
            ]
        );
    }
}
