<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\QrCode;

class QrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing QR codes
        QrCode::query()->delete();
        
        // Get all users
        $users = User::all();
        
        echo "Generating QR codes for " . $users->count() . " users...\n";
        
        // Generate QR codes for each user
        foreach ($users as $user) {
            // Generate a QR code for the user
            QrCode::create([
                'user_id' => $user->user_id,
                'code' => QrCode::generateCodeWithUserId($user->user_id),
                'is_active' => true,
            ]);
        }
        
        echo "QR codes generated successfully!\n";
    }
} 