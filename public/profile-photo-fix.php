<?php
/**
 * Profile Photo Fix Script
 * 
 * This script ensures that profile photos in storage/app/public/profile-photos
 * are properly symlinked or copied to public/storage/profile-photos for web access.
 */

// Set display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define paths
$storagePath = dirname(__DIR__) . '/storage';
$publicPath = dirname(__DIR__) . '/public';
$actualStorage = $storagePath . '/app/public';
$publicStorage = $publicPath . '/storage';
$profilePhotosStorage = $actualStorage . '/profile-photos';
$profilePhotosPublic = $publicStorage . '/profile-photos';

echo "<h1>Profile Photo Fix Utility</h1>";
echo "<p>This script ensures profile photos are correctly accessible from the web.</p>";

// Function to format file sizes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Function to check directory existence
function checkDirectory($path, $description) {
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
    echo "<h3>$description</h3>";
    echo "<p>Path: <code>$path</code></p>";
    
    if (file_exists($path)) {
        echo "<p style='color: green;'>✓ Directory exists</p>";
        
        if (is_readable($path)) {
            echo "<p style='color: green;'>✓ Directory is readable</p>";
        } else {
            echo "<p style='color: red;'>✗ Directory is not readable</p>";
        }
        
        if (is_writable($path)) {
            echo "<p style='color: green;'>✓ Directory is writable</p>";
        } else {
            echo "<p style='color: red;'>✗ Directory is not writable</p>";
        }
        
        // Check contents
        $files = scandir($path);
        $fileCount = count($files) - 2; // Subtract . and ..
        echo "<p>Contains $fileCount files/directories</p>";
        
        // List a few files as examples
        if ($fileCount > 0) {
            echo "<p>Sample contents:</p>";
            echo "<ul>";
            $counter = 0;
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    if ($counter < 5) {
                        $fullPath = $path . '/' . $file;
                        $size = is_file($fullPath) ? formatBytes(filesize($fullPath)) : 'Directory';
                        echo "<li>$file ($size)</li>";
                    }
                    $counter++;
                }
            }
            if ($counter > 5) {
                echo "<li>... and " . ($counter - 5) . " more files</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist</p>";
    }
    echo "</div>";
}

// Check the symlink status
echo "<h2>Symlink Status</h2>";
if (is_link($publicPath . '/storage')) {
    echo "<p style='color: green;'>✓ Storage symlink exists</p>";
    echo "<p>Target: " . readlink($publicPath . '/storage') . "</p>";
} else {
    echo "<p style='color: red;'>✗ Storage symlink does not exist</p>";
}

// Check the storage directories
echo "<h2>Directory Status</h2>";
checkDirectory($actualStorage, 'Actual storage directory (app/public)');
checkDirectory($publicStorage, 'Public storage directory (/public/storage)');
checkDirectory($profilePhotosStorage, 'Profile photos in storage');
checkDirectory($profilePhotosPublic, 'Profile photos in public');

// Fix profile photos directory
echo "<h2>Fix Profile Photos</h2>";

// 1. Ensure public/storage directory exists
if (!file_exists($publicStorage)) {
    if (mkdir($publicStorage, 0755, true)) {
        echo "<p style='color: green;'>✓ Created public storage directory</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create public storage directory</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Public storage directory exists</p>";
}

// 2. Ensure profile-photos directory exists in public/storage
if (!file_exists($profilePhotosPublic)) {
    if (mkdir($profilePhotosPublic, 0755, true)) {
        echo "<p style='color: green;'>✓ Created profile photos directory in public storage</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create profile photos directory in public storage</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Profile photos directory in public storage exists</p>";
}

// 3. Ensure profile-photos directory exists in storage/app/public
if (!file_exists($profilePhotosStorage)) {
    if (mkdir($profilePhotosStorage, 0755, true)) {
        echo "<p style='color: green;'>✓ Created profile photos directory in app storage</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create profile photos directory in app storage</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Profile photos directory in app storage exists</p>";
}

// 4. Copy profile photos between directories
if (file_exists($profilePhotosStorage) && file_exists($profilePhotosPublic)) {
    echo "<h3>Synchronizing Profile Photos</h3>";
    
    $storageFiles = scandir($profilePhotosStorage);
    $publicFiles = scandir($profilePhotosPublic);
    
    $copiedToPublic = 0;
    $failedToPublic = 0;
    
    // Copy from storage to public
    foreach ($storageFiles as $file) {
        if ($file !== '.' && $file !== '..' && !in_array($file, $publicFiles)) {
            if (copy($profilePhotosStorage . '/' . $file, $profilePhotosPublic . '/' . $file)) {
                $copiedToPublic++;
            } else {
                $failedToPublic++;
            }
        }
    }
    
    echo "<p>Copied $copiedToPublic files from storage to public.</p>";
    if ($failedToPublic > 0) {
        echo "<p style='color: red;'>Failed to copy $failedToPublic files.</p>";
    }
    
    $copiedToStorage = 0;
    $failedToStorage = 0;
    
    // Copy from public to storage
    foreach ($publicFiles as $file) {
        if ($file !== '.' && $file !== '..' && !in_array($file, $storageFiles)) {
            if (copy($profilePhotosPublic . '/' . $file, $profilePhotosStorage . '/' . $file)) {
                $copiedToStorage++;
            } else {
                $failedToStorage++;
            }
        }
    }
    
    echo "<p>Copied $copiedToStorage files from public to storage.</p>";
    if ($failedToStorage > 0) {
        echo "<p style='color: red;'>Failed to copy $failedToStorage files.</p>";
    }
    
    if ($copiedToPublic === 0 && $copiedToStorage === 0 && $failedToPublic === 0 && $failedToStorage === 0) {
        echo "<p style='color: green;'>✓ All profile photos are already synchronized</p>";
    }
} else {
    echo "<p style='color: red;'>Cannot synchronize files because one of the directories does not exist.</p>";
}

// Fix for Laravel storage:link
echo "<h2>Storage Symlink Fix</h2>";
echo "<p>If the storage symlink is missing, run this command in your project directory:</p>";
echo "<pre>php artisan storage:link</pre>";

echo "<h2>Database Check</h2>";
echo "<p>To ensure profile photos are properly linked in the database:</p>";
echo "<ol>";
echo "<li>Check that user.profile_photo_path values point to existing files</li>";
echo "<li>Ensure the URL construction in views uses the correct path</li>";
echo "</ol>";

echo "<h2>Next Steps</h2>";
echo "<p>After running this script:</p>";
echo "<ol>";
echo "<li>Check that profile photos appear correctly in the application</li>";
echo "<li>Verify that both storage/app/public/profile-photos and public/storage/profile-photos contain the same files</li>";
echo "<li>If issues persist, check the Laravel log files for additional error information</li>";
echo "</ol>";

// Button to rerun the script
echo '<p><a href="' . $_SERVER['PHP_SELF'] . '" style="display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Run Script Again</a></p>'; 