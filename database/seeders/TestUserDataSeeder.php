<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class TestUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update the Admin user with test data
        User::where('user_id', '110001')
            ->update([
                'gender' => 'Male',
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St, City',
                'mobile_number' => '1234567890',
            ]);
        
        echo "Admin user updated\n";
            
        // Update other test users
        User::where('user_id', '110007')
            ->update([
                'gender' => 'Female',
                'date_of_birth' => '1992-02-15',
                'address' => '456 Elm St, Town',
                'mobile_number' => '0987654321',
            ]);
        
        echo "Admin User (110007) updated\n";
            
        User::where('user_id', '110009')
            ->update([
                'gender' => 'Male',
                'date_of_birth' => '1985-07-20',
                'address' => '789 Oak St, Village',
                'mobile_number' => '5554443333',
            ]);
        
        echo "Secretary User (110009) updated\n";
            
        User::where('user_id', '110010')
            ->update([
                'gender' => 'Female',
                'date_of_birth' => '1988-12-10',
                'address' => '101 Pine St, County',
                'mobile_number' => '1112223333',
            ]);
        
        echo "Member User (110010) updated\n";
            
        echo "Test user data has been added successfully!\n";
    }
} 