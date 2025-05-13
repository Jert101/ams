<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\QrCode;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if roles exist, if not run the RoleSeeder first
        if (Role::count() === 0) {
            $this->call(RoleSeeder::class);
        }
        
        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $officerRole = Role::where('name', 'Officer')->first();
        $secretaryRole = Role::where('name', 'Secretary')->first();
        $memberRole = Role::where('name', 'Member')->first();

        // Create Admin user if it doesn't exist
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
                'phone' => '1234567890',
            ]
        );

        // Create Officer user if it doesn't exist
        $officer = User::updateOrCreate(
            ['email' => 'officer@example.com'],
            [
                'name' => 'Officer',
                'password' => Hash::make('password'),
                'role_id' => $officerRole->id,
                'email_verified_at' => now(),
                'phone' => '2345678901',
            ]
        );

        // Create Secretary user if it doesn't exist
        $secretary = User::updateOrCreate(
            ['email' => 'secretary@example.com'],
            [
                'name' => 'Secretary',
                'password' => Hash::make('password'),
                'role_id' => $secretaryRole->id,
                'email_verified_at' => now(),
                'phone' => '3456789012',
            ]
        );

        // Create Member user if it doesn't exist
        $member = User::updateOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Member',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
                'email_verified_at' => now(),
                'phone' => '4567890123',
            ]
        );

        // Generate QR codes for each user if the QrCode model exists
        try {
            foreach ([$admin, $officer, $secretary, $member] as $user) {
                QrCode::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'code' => method_exists(QrCode::class, 'generateUniqueCode') 
                            ? QrCode::generateUniqueCode() 
                            : uniqid('qr_'),
                        'is_active' => true,
                    ]
                );
            }
        } catch (\Exception $e) {
            // QrCode model might not exist or have different structure
            // Just continue without creating QR codes
        }
    }
}
