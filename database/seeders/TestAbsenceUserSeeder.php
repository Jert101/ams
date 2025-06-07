<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestAbsenceUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Member role ID
        $memberRole = Role::where('name', 'Member')->first();
        
        if (!$memberRole) {
            // Create Member role if it doesn't exist
            $memberRole = Role::create([
                'name' => 'Member',
                'description' => 'Regular member of the organization'
            ]);
        }
        
        // Create or update test user for 3 consecutive absences
        User::updateOrCreate(
            ['email' => 'jertcatadman@gmail.com'],
            [
                'name' => 'Rhia Mae Ycoy',
                'email' => 'jertcatadman@gmail.com',
                'password' => Hash::make('password123'),
                'role_id' => $memberRole->id,
                'email_verified_at' => now(),
                'phone' => '09123456789',
                'address' => '123 Test Street, Test City',
                'user_id' => '3001',
                'remember_token' => Str::random(10),
            ]
        );
        
        // Create or update test user for 4 consecutive absences
        User::updateOrCreate(
            ['email' => 'jersoncatadman88@gmail.com'],
            [
                'name' => 'Jerson Catadman',
                'email' => 'jersoncatadman88@gmail.com',
                'password' => Hash::make('password123'),
                'role_id' => $memberRole->id,
                'email_verified_at' => now(),
                'phone' => '09987654321',
                'address' => '456 Test Avenue, Test City',
                'user_id' => '4001',
                'remember_token' => Str::random(10),
            ]
        );
        
        $this->command->info('Test absence users created successfully.');
    }
} 