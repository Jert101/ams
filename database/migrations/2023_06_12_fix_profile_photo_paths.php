<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // Get all users with profile photos
        $users = DB::table('users')
            ->whereNotNull('profile_photo_path')
            ->where('profile_photo_path', '!=', 'kofa.png')
            ->get(['user_id', 'profile_photo_path']);

        foreach ($users as $user) {
            // Extract the filename from the path
            $filename = basename($user->profile_photo_path);
            
            // Update to just profile-photos/filename.ext format
            if (strpos($user->profile_photo_path, 'storage/profile-photos/') === 0) {
                $newPath = 'profile-photos/' . $filename;
                
                // Update the user record
                DB::table('users')
                    ->where('user_id', $user->user_id)
                    ->update(['profile_photo_path' => $newPath]);
                
                Log::info("Updated profile photo path for user {$user->user_id} from {$user->profile_photo_path} to {$newPath}");
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // This migration cannot be reversed easily
    }
}; 