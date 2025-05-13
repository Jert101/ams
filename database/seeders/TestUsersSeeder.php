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
        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $officerRole = Role::where('name', 'Officer')->first();
        $secretaryRole = Role::where('name', 'Secretary')->first();
        $memberRole = Role::where('name', 'Member')->first();

        // Create Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
            'phone' => '1234567890',
            'address' => '123 Admin St, Admin City',
            'gender' => 'male',
        ]);

        // Create Officer user
        User::create([
            'name' => 'Officer User',
            'email' => 'officer@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $officerRole->id,
            'email_verified_at' => now(),
            'phone' => '2345678901',
            'address' => '456 Officer St, Officer City',
            'gender' => 'female',
        ]);

        // Create Secretary user
        User::create([
            'name' => 'Secretary User',
            'email' => 'secretary@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $secretaryRole->id,
            'email_verified_at' => now(),
            'phone' => '3456789012',
            'address' => '789 Secretary St, Secretary City',
            'gender' => 'male',
        ]);

        // Create Member user
        User::create([
            'name' => 'Member User',
            'email' => 'member@kofa.com',
            'password' => Hash::make('password'),
            'role_id' => $memberRole->id,
            'email_verified_at' => now(),
            'phone' => '4567890123',
            'address' => '101 Member St, Member City',
            'gender' => 'female',
        ]);
    }
}
