<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $adminRoleId = Role::where('name', 'Admin')->first()->id;
        $officerRoleId = Role::where('name', 'Officer')->first()->id;
        $secretaryRoleId = Role::where('name', 'Secretary')->first()->id;
        $memberRoleId = Role::where('name', 'Member')->first()->id;

        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin_test@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRoleId,
            'email_verified_at' => now(),
            'user_id' => 110007,
            'profile_photo_path' => 'kofa.png',
            'mobile_number' => '1111111111',
            'approval_status' => 'approved',
        ]);

        // Officer User
        User::create([
            'name' => 'Officer User',
            'email' => 'officer@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $officerRoleId,
            'email_verified_at' => now(),
            'user_id' => 110008,
            'profile_photo_path' => 'kofa.png',
            'mobile_number' => '2222222222',
            'approval_status' => 'approved',
        ]);

        // Secretary User
        User::create([
            'name' => 'Secretary User',
            'email' => 'secretary@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $secretaryRoleId,
            'email_verified_at' => now(),
            'user_id' => 110009,
            'profile_photo_path' => 'kofa.png',
            'mobile_number' => '3333333333',
            'approval_status' => 'approved',
        ]);

        // Member User
        User::create([
            'name' => 'Member User',
            'email' => 'member@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $memberRoleId,
            'email_verified_at' => now(),
            'user_id' => 110010,
            'profile_photo_path' => 'kofa.png',
            'mobile_number' => '4444444444',
            'approval_status' => 'approved',
        ]);
    }
}
