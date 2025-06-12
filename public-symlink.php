<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Public Storage Symlink Fix</h1>";

// Show server information
echo "<h2>Server Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "</pre>";

// Define paths - using document root to ensure we're in the public directory
$publicRoot = $_SERVER['DOCUMENT_ROOT'];
$publicStoragePath = $publicRoot . '/storage';
$storageAppPublicPath = dirname($publicRoot) . '/storage/app/public';

echo "<h2>Path Information</h2>";
echo "<pre>";
echo "Public root: $publicRoot\n";
echo "Public storage path: $publicStoragePath\n";
echo "Storage app public path: $storageAppPublicPath\n";
echo "</pre>";

// Check if storage/app/public directory exists
echo "<h2>Directory Check</h2>";

if (file_exists($storageAppPublicPath)) {
    echo "<p style='color:green;'>storage/app/public directory exists</p>";
} else {
    echo "<p style='color:red;'>storage/app/public directory does not exist</p>";
    echo "<p>Attempting to create it...</p>";
    
    if (mkdir($storageAppPublicPath, 0777, true)) {
        echo "<p style='color:green;'>Successfully created storage/app/public directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create storage/app/public directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Check if public/storage exists and what type it is
if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        echo "<p style='color:green;'>public/storage is already a symlink</p>";
        echo "<p>Target: " . readlink($publicStoragePath) . "</p>";
        
        // Check if it's pointing to the correct location
        if (readlink($publicStoragePath) == $storageAppPublicPath) {
            echo "<p style='color:green;'>Symlink is pointing to the correct location</p>";
        } else {
            echo "<p style='color:red;'>Symlink is pointing to the wrong location</p>";
            echo "<p>Current target: " . readlink($publicStoragePath) . "</p>";
            echo "<p>Expected target: " . $storageAppPublicPath . "</p>";
            
            // Remove the incorrect symlink
            if (unlink($publicStoragePath)) {
                echo "<p style='color:green;'>Removed incorrect symlink</p>";
            } else {
                echo "<p style='color:red;'>Failed to remove incorrect symlink</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>public/storage exists but is not a symlink</p>";
        echo "<p>It's a regular directory or file</p>";
        
        // Try to rename it
        $backupDir = $publicRoot . '/storage_backup_' . time();
        if (rename($publicStoragePath, $backupDir)) {
            echo "<p style='color:green;'>Renamed existing directory to $backupDir</p>";
        } else {
            echo "<p style='color:red;'>Failed to rename existing directory</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
} else {
    echo "<p>public/storage does not exist</p>";
}

// Create the public/storage directory structure manually
echo "<h2>Creating Public Storage Directory</h2>";

// Create public/storage if it doesn't exist
if (!file_exists($publicStoragePath)) {
    if (mkdir($publicStoragePath, 0777, true)) {
        echo "<p style='color:green;'>Created public/storage directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create public/storage directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
        exit;
    }
}

// Create public/storage/profile-photos directory
$publicProfilePhotosDir = $publicStoragePath . '/profile-photos';
if (!file_exists($publicProfilePhotosDir)) {
    if (mkdir($publicProfilePhotosDir, 0777, true)) {
        echo "<p style='color:green;'>Created public/storage/profile-photos directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create public/storage/profile-photos directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Create .htaccess file to ensure proper access
$htaccessContent = "Options +FollowSymLinks\nAllow from all";
if (file_put_contents($publicStoragePath . '/.htaccess', $htaccessContent)) {
    echo "<p style='color:green;'>Created .htaccess file in public/storage</p>";
} else {
    echo "<p style='color:red;'>Failed to create .htaccess file</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Create a test file in storage/app/public/profile-photos
echo "<h2>Testing File Copy Mechanism</h2>";

// Create storage/app/public/profile-photos if it doesn't exist
$storageProfilePhotosDir = $storageAppPublicPath . '/profile-photos';
if (!file_exists($storageProfilePhotosDir)) {
    if (mkdir($storageProfilePhotosDir, 0777, true)) {
        echo "<p style='color:green;'>Created storage/app/public/profile-photos directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create storage/app/public/profile-photos directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Create a test file
$testFile = $storageProfilePhotosDir . '/test-' . time() . '.txt';
$testContent = "Test file created at " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "<p style='color:green;'>Created test file in storage/app/public/profile-photos</p>";
    
    // Copy the test file to public/storage/profile-photos
    $publicTestFile = $publicProfilePhotosDir . '/' . basename($testFile);
    if (copy($testFile, $publicTestFile)) {
        echo "<p style='color:green;'>Copied test file to public/storage/profile-photos</p>";
        
        // Test if the file is accessible via web
        $testUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/profile-photos/' . basename($testFile);
        echo "<p>Test file URL: <a href='$testUrl' target='_blank'>$testUrl</a></p>";
        echo "<p>Click the link to verify the file is accessible.</p>";
    } else {
        echo "<p style='color:red;'>Failed to copy test file to public/storage/profile-photos</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
} else {
    echo "<p style='color:red;'>Failed to create test file in storage/app/public/profile-photos</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Create a function to copy files from storage to public
echo "<h2>Creating Copy Function</h2>";

// Create a file that will contain the copy function
$copyFunctionFile = $publicRoot . '/copy-profile-photos.php';
$copyFunctionContent = <<<'EOT'
<?php
// This file contains a function to copy profile photos from storage to public

function copyProfilePhotosToPublic() {
    $storageDir = dirname(__DIR__) . '/storage/app/public/profile-photos';
    $publicDir = __DIR__ . '/storage/profile-photos';
    
    if (!file_exists($storageDir)) {
        return ['success' => false, 'message' => 'Storage directory does not exist'];
    }
    
    if (!file_exists($publicDir)) {
        if (!mkdir($publicDir, 0777, true)) {
            return ['success' => false, 'message' => 'Failed to create public directory'];
        }
    }
    
    $files = scandir($storageDir);
    $copied = 0;
    $failed = 0;
    $errors = [];
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && is_file($storageDir . '/' . $file)) {
            if (copy($storageDir . '/' . $file, $publicDir . '/' . $file)) {
                $copied++;
            } else {
                $failed++;
                $errors[] = $file;
            }
        }
    }
    
    return [
        'success' => ($failed == 0),
        'copied' => $copied,
        'failed' => $failed,
        'errors' => $errors
    ];
}

// Run the copy function if this file is accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(copyProfilePhotosToPublic());
}
EOT;

if (file_put_contents($copyFunctionFile, $copyFunctionContent)) {
    echo "<p style='color:green;'>Created copy function file at $copyFunctionFile</p>";
    echo "<p>You can run this file directly to copy all profile photos from storage to public.</p>";
} else {
    echo "<p style='color:red;'>Failed to create copy function file</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Modify the ProfileController to use the copy function
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Make sure both storage/app/public/profile-photos and public/storage/profile-photos directories exist and are writable</li>";
echo "<li>Try uploading a profile picture again as an admin</li>";
echo "<li>If the profile picture still doesn't display, run the copy-profile-photos.php script to copy all profile photos from storage to public</li>";
echo "<li>Check if the database has the correct profile_photo_path values (should be like 'profile-photos/filename.jpg')</li>";
echo "</ol>";

// Create a simple script to view all profile photos
$viewPhotosFile = $publicRoot . '/view-profile-photos.php';
$viewPhotosContent = <<<'EOT'
<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>View Profile Photos</h1>";

// Define paths
$publicDir = __DIR__ . '/storage/profile-photos';

if (!file_exists($publicDir)) {
    echo "<p style='color:red;'>Public profile photos directory does not exist!</p>";
    exit;
}

$files = scandir($publicDir);
$photoCount = 0;

echo "<div style='display: flex; flex-wrap: wrap;'>";

foreach ($files as $file) {
    if ($file != '.' && $file != '..' && is_file($publicDir . '/' . $file)) {
        $photoCount++;
        $url = '/storage/profile-photos/' . $file;
        echo "<div style='margin: 10px; text-align: center;'>";
        echo "<img src='$url' style='max-width: 200px; max-height: 200px; border: 1px solid #ddd;'>";
        echo "<p>$file</p>";
        echo "</div>";
    }
}

echo "</div>";

if ($photoCount == 0) {
    echo "<p>No profile photos found in the public directory.</p>";
}
EOT;

if (file_put_contents($viewPhotosFile, $viewPhotosContent)) {
    echo "<p style='color:green;'>Created view photos file at $viewPhotosFile</p>";
    echo "<p>You can access this file to view all profile photos in the public directory.</p>";
} else {
    echo "<p style='color:red;'>Failed to create view photos file</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Add a hook to the ProfileController to copy files
$hookFile = dirname($publicRoot) . '/app/Hooks/ProfilePhotoHook.php';
$hookDir = dirname($publicRoot) . '/app/Hooks';

if (!file_exists($hookDir)) {
    if (mkdir($hookDir, 0777, true)) {
        echo "<p style='color:green;'>Created Hooks directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create Hooks directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

$hookContent = <<<'EOT'
<?php

namespace App\Hooks;

class ProfilePhotoHook
{
    public static function copyToPublic($path)
    {
        $storageFile = storage_path('app/public/' . $path);
        $publicFile = public_path('storage/' . $path);
        
        // Ensure the directory exists
        $publicDir = dirname($publicFile);
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0777, true);
        }
        
        // Copy the file
        if (file_exists($storageFile)) {
            return copy($storageFile, $publicFile);
        }
        
        return false;
    }
}
EOT;

if (file_put_contents($hookFile, $hookContent)) {
    echo "<p style='color:green;'>Created ProfilePhotoHook class</p>";
} else {
    echo "<p style='color:red;'>Failed to create ProfilePhotoHook class</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

echo "<h2>Final Instructions</h2>";
echo "<p>After running this script:</p>";
echo "<ol>";
echo "<li>Try uploading a profile picture as an admin</li>";
echo "<li>If the profile picture still doesn't display, access /copy-profile-photos.php to copy all photos from storage to public</li>";
echo "<li>You can view all profile photos by accessing /view-profile-photos.php</li>";
echo "</ol>";
?> 