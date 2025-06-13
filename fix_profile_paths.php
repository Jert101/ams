<?php
/**
 * Fix Profile Photo Paths Script
 * 
 * This script:
 * 1. Updates profile_photo_path values in the database to use 'profile-photos/' format
 * 2. Ensures all profile photos are in the root level profile-photos directory
 * 
 * Usage: Place this file in the root directory of your Laravel project and run:
 * php fix_profile_paths.php
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Create profile-photos directory if it doesn't exist
$rootProfilePhotosPath = __DIR__ . '/profile-photos';
if (!file_exists($rootProfilePhotosPath)) {
    mkdir($rootProfilePhotosPath, 0777, true);
    echo "Created directory: {$rootProfilePhotosPath}\n";
}

// Get all users with profile photos
$users = DB::table('users')
    ->whereNotNull('profile_photo_path')
    ->where('profile_photo_path', '!=', 'kofa.png')
    ->get(['user_id', 'name', 'profile_photo_path']);

echo "Found " . count($users) . " users with profile photos to process\n";

foreach ($users as $user) {
    echo "Processing user {$user->user_id} ({$user->name}): {$user->profile_photo_path}\n";
    
    // Extract the filename from the path
    $filename = basename($user->profile_photo_path);
    $newPath = 'profile-photos/' . $filename;
    
    // Check if the file exists in various locations
    $fileFound = false;
    
    // Check in storage/app/public/profile-photos
    $storagePath = storage_path('app/public/profile-photos/' . $filename);
    if (file_exists($storagePath)) {
        echo "  Found in storage: {$storagePath}\n";
        // Copy to root profile-photos directory
        copy($storagePath, $rootProfilePhotosPath . '/' . $filename);
        echo "  Copied to: {$rootProfilePhotosPath}/{$filename}\n";
        $fileFound = true;
    }
    
    // Check in public/storage/profile-photos
    $publicStoragePath = public_path('storage/profile-photos/' . $filename);
    if (file_exists($publicStoragePath)) {
        echo "  Found in public storage: {$publicStoragePath}\n";
        // Copy to root profile-photos directory if not already done
        if (!file_exists($rootProfilePhotosPath . '/' . $filename)) {
            copy($publicStoragePath, $rootProfilePhotosPath . '/' . $filename);
            echo "  Copied to: {$rootProfilePhotosPath}/{$filename}\n";
        }
        $fileFound = true;
    }
    
    // Check in public/profile-photos
    $publicPath = public_path('profile-photos/' . $filename);
    if (file_exists($publicPath)) {
        echo "  Found in public: {$publicPath}\n";
        // Copy to root profile-photos directory if not already done
        if (!file_exists($rootProfilePhotosPath . '/' . $filename)) {
            copy($publicPath, $rootProfilePhotosPath . '/' . $filename);
            echo "  Copied to: {$rootProfilePhotosPath}/{$filename}\n";
        }
        $fileFound = true;
    }
    
    // If file was found in any location, update the database
    if ($fileFound) {
        // Update the user record
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update(['profile_photo_path' => $newPath]);
        echo "  Updated database path to: {$newPath}\n";
    } else {
        echo "  WARNING: Could not find profile photo file for user {$user->user_id}\n";
    }
    
    echo "  Done processing user {$user->user_id}\n";
    echo "---------------------------------------------\n";
}

echo "Script completed successfully!\n"; 