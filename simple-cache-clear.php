<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Laravel Cache Cleaner</h1>";

// Define paths
$basePath = dirname(__DIR__);
$cachePaths = [
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/framework/sessions',
    $basePath . '/storage/framework/views',
    $basePath . '/bootstrap/cache',
];

echo "<h2>Clearing Cache Directories</h2>";

// Function to clear a directory
function clearDirectory($dir) {
    if (!is_dir($dir)) {
        echo "<p>Directory does not exist: $dir</p>";
        return;
    }
    
    echo "<p>Clearing directory: $dir</p>";
    
    $files = scandir($dir);
    $count = 0;
    
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && $file != ".gitignore") {
            $path = $dir . "/" . $file;
            if (is_dir($path)) {
                clearDirectory($path);
                if (@rmdir($path)) {
                    $count++;
                }
            } else {
                if (@unlink($path)) {
                    $count++;
                }
            }
        }
    }
    
    echo "<p>Removed $count items from $dir</p>";
}

// Clear each cache directory
foreach ($cachePaths as $path) {
    clearDirectory($path);
}

echo "<h2>Creating Required Directories</h2>";

// Make sure all required directories exist
$requiredDirs = [
    $basePath . '/storage/framework/cache',
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/framework/sessions',
    $basePath . '/storage/framework/views',
    $basePath . '/storage/framework/testing',
    $basePath . '/storage/logs',
    $basePath . '/bootstrap/cache',
];

foreach ($requiredDirs as $dir) {
    if (!file_exists($dir)) {
        echo "<p>Creating directory: $dir</p>";
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color:green;'>Successfully created directory</p>";
        } else {
            echo "<p style='color:red;'>Failed to create directory</p>";
        }
    } else {
        echo "<p>Directory exists: $dir</p>";
        // Update permissions
        if (@chmod($dir, 0777)) {
            echo "<p style='color:green;'>Updated permissions to 0777</p>";
        }
    }
}

echo "<h2>Cache Cleared Successfully!</h2>";
echo "<p>All Laravel cache directories have been cleared and required directories have been created with proper permissions.</p>";
echo "<p>You should now be able to access your site without the 'Please provide a valid cache path' error.</p>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 