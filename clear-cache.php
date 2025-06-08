<?php
/**
 * Cache clearing script for Laravel application
 * 
 * This script provides a way to clear Laravel application caches when database access is not working properly.
 * It directly clears the cache files without needing to use artisan commands.
 */

// Check for a secret key to prevent unauthorized cache clearing
$secretKey = $_GET['key'] ?? '';
if ($secretKey !== 'kofa-cache-clear') {
    die('Invalid access key. For security reasons, you need to provide the correct key parameter.');
}

// Define cache directories to clear
$cacheDirs = [
    'bootstrap/cache/',
    'storage/framework/cache/',
    'storage/framework/views/',
    'storage/framework/sessions/',
    'storage/logs/',
];

// Backup files to keep
$keepFiles = [
    '.gitignore',
    '.gitkeep',
];

// Get the base path
$basePath = __DIR__;

// Function to clear directory content
function clearDirectory($dir, $keepFiles = []) {
    if (!is_dir($dir)) {
        return "✗ Directory not found: $dir\n";
    }
    
    $files = scandir($dir);
    $success = true;
    
    foreach ($files as $file) {
        // Skip . and .. directories and files to keep
        if ($file === '.' || $file === '..' || in_array($file, $keepFiles)) {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        
        if (is_dir($path)) {
            // For directories, clear recursively
            clearDirectory($path, $keepFiles);
            
            // Only remove the directory if it's not the root directories we were asked to clear
            if (strpos($path, 'storage/framework') !== false && basename($path) !== 'framework' && 
                basename($path) !== 'cache' && basename($path) !== 'views' && basename($path) !== 'sessions') {
                // Try to remove the directory
                if (!@rmdir($path)) {
                    $success = false;
                }
            }
        } else {
            // For files, delete directly
            if (!@unlink($path)) {
                $success = false;
            }
        }
    }
    
    return $success ? "✓ Cleared: $dir\n" : "✗ Failed to fully clear: $dir\n";
}

// Start output buffer to collect results
ob_start();
echo "<pre>\n";
echo "=== KOFA Attendance Management System Cache Cleaner ===\n\n";
echo "Started cache clearing at: " . date('Y-m-d H:i:s') . "\n\n";

// Clear each cache directory
foreach ($cacheDirs as $dir) {
    $fullPath = $basePath . DIRECTORY_SEPARATOR . $dir;
    echo clearDirectory($fullPath, $keepFiles);
}

// Generate a fresh application key if requested
if (isset($_GET['refresh_key']) && $_GET['refresh_key'] === 'true') {
    $envFile = $basePath . DIRECTORY_SEPARATOR . '.env';
    
    if (file_exists($envFile) && is_writable($envFile)) {
        // Generate a new key
        $newKey = 'base64:' . base64_encode(random_bytes(32));
        
        // Read the .env file
        $envContents = file_get_contents($envFile);
        
        // Replace the APP_KEY line
        $envContents = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $newKey, $envContents);
        
        // Write back to the file
        if (file_put_contents($envFile, $envContents) !== false) {
            echo "✓ Generated new application key: $newKey\n";
        } else {
            echo "✗ Failed to write new application key\n";
        }
    } else {
        echo "✗ .env file not found or not writable\n";
    }
}

echo "\nCache clearing completed at: " . date('Y-m-d H:i:s') . "\n";
echo "You should now be able to access your application without issues.\n";
echo "If problems persist, please run 'php artisan optimize:clear' from the command line.\n";
echo "</pre>";

// Flush the output buffer
ob_end_flush(); 