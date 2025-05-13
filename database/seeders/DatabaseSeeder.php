<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the role seeder first to create roles
        $this->call(RoleSeeder::class);
        
        // Determine which user seeder to run based on environment
        if (app()->environment('production')) {
            // Use the standard UserSeeder for production
            $this->call(UserSeeder::class);
        } else {
            // For development/testing environments
            
            // Create admin user
            $this->call(AdminSeeder::class);
            
            // Create test users for each role
            $this->call(TestUsersSeeder::class);
        }
    }
}
