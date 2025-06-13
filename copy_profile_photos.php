<?php
/**
 * Copy Profile Photos Script
 * 
 * This script copies profile photos from various locations to the root level profile-photos directory.
 * Run this script on your InfinityFree hosting after running the SQL script to update the database.
 * 
 * Usage: Upload this file to your InfinityFree hosting and run it in the browser.
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create profile-photos directory if it doesn't exist
$rootProfilePhotosPath = __DIR__ . '/profile-photos';
if (!file_exists($rootProfilePhotosPath)) {
    if (mkdir($rootProfilePhotosPath, 0777, true)) {
        echo "Created directory: {$rootProfilePhotosPath}<br>";
    } else {
        echo "Failed to create directory: {$rootProfilePhotosPath}<br>";
    }
}

// Set directory permissions
chmod($rootProfilePhotosPath, 0777);
echo "Set permissions on {$rootProfilePhotosPath}<br>";

// Define source directories to check for profile photos
$sourceDirectories = [
    __DIR__ . '/storage/app/public/profile-photos',
    __DIR__ . '/public/storage/profile-photos',
    __DIR__ . '/public/profile-photos'
];

// Process each source directory
foreach ($sourceDirectories as $sourceDir) {
    if (file_exists($sourceDir) && is_dir($sourceDir)) {
        echo "Processing directory: {$sourceDir}<br>";
        
        // Get all files in the directory
        $files = scandir($sourceDir);
        foreach ($files as $file) {
            // Skip . and .. directories
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $sourcePath = $sourceDir . '/' . $file;
            $destPath = $rootProfilePhotosPath . '/' . $file;
            
            // Only copy files, not directories
            if (is_file($sourcePath)) {
                echo "Found file: {$file}<br>";
                
                // Copy the file if it doesn't exist in the destination
                if (!file_exists($destPath)) {
                    if (copy($sourcePath, $destPath)) {
                        echo "Copied to: {$destPath}<br>";
                    } else {
                        echo "Failed to copy: {$sourcePath} to {$destPath}<br>";
                    }
                } else {
                    echo "File already exists in destination: {$destPath}<br>";
                }
            }
        }
    } else {
        echo "Directory does not exist or is not accessible: {$sourceDir}<br>";
    }
}

echo "<br>Script completed successfully!<br>"; 