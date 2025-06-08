<?php
/**
 * Profile Photo Fix Script for Deployed Environment
 * 
 * This script resolves profile photo display issues by:
 * 1. Ensuring storage/app/public/profile-photos directory exists
 * 2. Ensuring public/storage/profile-photos directory exists
 * 3. Copying photos between directories to ensure they exist in both places
 * 4. Checking database for correct profile photo paths
 */

// Set display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define paths using absolute server paths
$rootPath = realpath(dirname(__DIR__));
$storagePath = $rootPath . '/storage';
$publicPath = $rootPath . '/public';
$actualStorage = $storagePath . '/app/public';
$publicStorage = $publicPath . '/storage';
$profilePhotosStorage = $actualStorage . '/profile-photos';
$profilePhotosPublic = $publicStorage . '/profile-photos';

echo "<h1>Profile Photo Fix for Production Server</h1>";
echo "<p>Server path: {$rootPath}</p>";

// Function to format file sizes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Check directory existence and create if needed
function ensureDirectoryExists($path) {
    echo "<h3>Checking directory: $path</h3>";
    
    if (file_exists($path)) {
        echo "<p style='color: green;'>✓ Directory already exists</p>";
    } else {
        if (mkdir($path, 0755, true)) {
            echo "<p style='color: green;'>✓ Directory created successfully</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create directory</p>";
        }
    }
    
    // Check permissions
    if (is_readable($path)) {
        echo "<p style='color: green;'>✓ Directory is readable</p>";
    } else {
        echo "<p style='color: red;'>✗ Directory is not readable</p>";
        if (chmod($path, 0755)) {
            echo "<p style='color: green;'>✓ Permissions updated to readable</p>";
        }
    }
    
    if (is_writable($path)) {
        echo "<p style='color: green;'>✓ Directory is writable</p>";
    } else {
        echo "<p style='color: red;'>✗ Directory is not writable</p>";
        if (chmod($path, 0755)) {
            echo "<p style='color: green;'>✓ Permissions updated to writable</p>";
        }
    }
    
    return file_exists($path) && is_readable($path) && is_writable($path);
}

// Copy files between directories
function synchronizeFiles($sourceDir, $destDir) {
    if (!file_exists($sourceDir) || !file_exists($destDir)) {
        echo "<p style='color: red;'>✗ Cannot synchronize. One of the directories doesn't exist.</p>";
        return [0, 0];
    }
    
    $sourceFiles = scandir($sourceDir);
    $destFiles = scandir($destDir);
    $copied = 0;
    $failed = 0;
    
    foreach ($sourceFiles as $file) {
        if ($file == '.' || $file == '..') continue;
        
        if (!in_array($file, $destFiles)) {
            $sourcePath = $sourceDir . '/' . $file;
            $destPath = $destDir . '/' . $file;
            
            if (copy($sourcePath, $destPath)) {
                echo "<p>Copied: $file</p>";
                $copied++;
            } else {
                echo "<p style='color: red;'>Failed to copy: $file</p>";
                $failed++;
            }
        }
    }
    
    return [$copied, $failed];
}

// 1. Check storage symlink
echo "<h2>Storage Symlink Check</h2>";
if (is_link($publicPath . '/storage')) {
    echo "<p style='color: green;'>✓ Storage symlink exists</p>";
    echo "<p>Target: " . readlink($publicPath . '/storage') . "</p>";
} else {
    echo "<p style='color: orange;'>⚠ Storage symlink does not exist. Will use file copying as fallback.</p>";
    
    // Create the storage directory in public if it doesn't exist
    if (!file_exists($publicStorage)) {
        if (mkdir($publicStorage, 0755, true)) {
            echo "<p style='color: green;'>✓ Created public storage directory</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create public storage directory</p>";
        }
    }
}

// 2. Check and ensure directories
echo "<h2>Directory Structure</h2>";
$storageOk = ensureDirectoryExists($actualStorage);
$publicStorageOk = ensureDirectoryExists($publicStorage);
$profileStorageOk = ensureDirectoryExists($profilePhotosStorage);
$profilePublicOk = ensureDirectoryExists($profilePhotosPublic);

// 3. Synchronize files
echo "<h2>Synchronizing Files</h2>";
if ($profileStorageOk && $profilePublicOk) {
    echo "<h3>Copying from storage to public</h3>";
    list($copied, $failed) = synchronizeFiles($profilePhotosStorage, $profilePhotosPublic);
    echo "<p>Copied $copied files, failed to copy $failed files</p>";
    
    echo "<h3>Copying from public to storage</h3>";
    list($copied, $failed) = synchronizeFiles($profilePhotosPublic, $profilePhotosStorage);
    echo "<p>Copied $copied files, failed to copy $failed files</p>";
} else {
    echo "<p style='color: red;'>Cannot synchronize files because directories are not properly set up.</p>";
}

// 4. Check for default image
echo "<h2>Default Image Check</h2>";
if (file_exists($publicPath . '/kofa.png')) {
    echo "<p style='color: green;'>✓ Default profile image (kofa.png) exists in public directory</p>";
} else {
    echo "<p style='color: red;'>✗ Default profile image (kofa.png) missing from public directory</p>";
    
    // Check if it exists in storage
    if (file_exists($storagePath . '/app/public/kofa.png')) {
        if (copy($storagePath . '/app/public/kofa.png', $publicPath . '/kofa.png')) {
            echo "<p style='color: green;'>✓ Copied default image from storage to public</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to copy default image</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Default image not found in storage either</p>";
    }
}

// 5. Test database connection
echo "<h2>Database Profile Photos Check</h2>";
try {
    // Create database connection using Laravel's configuration
    require_once $rootPath . '/vendor/autoload.php';
    
    $app = require_once $rootPath . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $users = DB::table('users')->whereNotNull('profile_photo_path')->get();
    
    echo "<p>Found " . count($users) . " users with profile photos in database</p>";
    
    $missingPhotos = 0;
    $validPhotos = 0;
    
    foreach ($users as $user) {
        $photoPath = $user->profile_photo_path;
        
        // Skip URLs
        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            $validPhotos++;
            continue;
        }
        
        // Check if file exists in public storage
        $publicPhotoPath = $publicStorage . '/' . $photoPath;
        $actualPhotoPath = $actualStorage . '/' . $photoPath;
        
        if (file_exists($publicPhotoPath)) {
            $validPhotos++;
        } else {
            $missingPhotos++;
            echo "<p style='color: orange;'>User ID {$user->id}: Photo missing from public storage: {$photoPath}</p>";
            
            // Try to copy from actual storage if it exists there
            if (file_exists($actualPhotoPath)) {
                $dirName = dirname($publicPhotoPath);
                if (!file_exists($dirName)) {
                    mkdir($dirName, 0755, true);
                }
                
                if (copy($actualPhotoPath, $publicPhotoPath)) {
                    echo "<p style='color: green;'>✓ Copied missing photo for user {$user->id}</p>";
                    $missingPhotos--;
                    $validPhotos++;
                }
            }
        }
    }
    
    echo "<p>Valid photos: $validPhotos, Missing photos: $missingPhotos</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error connecting to database: " . $e->getMessage() . "</p>";
}

// 6. Summary and next steps
echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li>Storage symlink: " . (is_link($publicPath . '/storage') ? "Exists" : "Does not exist") . "</li>";
echo "<li>Profile photos directory in storage: " . ($profileStorageOk ? "OK" : "Issues detected") . "</li>";
echo "<li>Profile photos directory in public: " . ($profilePublicOk ? "OK" : "Issues detected") . "</li>";
echo "<li>Default profile image: " . (file_exists($publicPath . '/kofa.png') ? "Available" : "Missing") . "</li>";
echo "</ul>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Check that profile photos appear correctly in the application now</li>";
echo "<li>If photos still don't appear, clear your browser cache or try in a private/incognito window</li>";
echo "<li>If still having issues, the server might need to run the Laravel command: <code>php artisan storage:link</code></li>";
echo "</ol>";

// Button to rerun the script
echo '<p><a href="' . $_SERVER['PHP_SELF'] . '" style="display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Run Script Again</a></p>'; 