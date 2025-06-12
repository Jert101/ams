<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Clear Laravel View Cache</h1>";

// Define paths
$basePath = __DIR__;
$viewsPath = $basePath . '/storage/framework/views';
$cachePath = $basePath . '/bootstrap/cache';

// Clear views
if (file_exists($viewsPath)) {
    $files = glob($viewsPath . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            if (unlink($file)) {
                echo "<p style='color:green;'>✅ Deleted view file: " . basename($file) . "</p>";
                $count++;
            } else {
                echo "<p style='color:red;'>❌ Failed to delete view file: " . basename($file) . "</p>";
            }
        }
    }
    echo "<p>Deleted $count view files</p>";
} else {
    echo "<p style='color:orange;'>⚠️ Views directory does not exist: $viewsPath</p>";
    echo "<p>Creating directory...</p>";
    if (mkdir($viewsPath, 0777, true)) {
        echo "<p style='color:green;'>✅ Created directory: $viewsPath</p>";
        chmod($viewsPath, 0777);
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory</p>";
    }
}

// Clear bootstrap cache
if (file_exists($cachePath)) {
    $files = glob($cachePath . '/*.php');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            if (unlink($file)) {
                echo "<p style='color:green;'>✅ Deleted cache file: " . basename($file) . "</p>";
                $count++;
            } else {
                echo "<p style='color:red;'>❌ Failed to delete cache file: " . basename($file) . "</p>";
            }
        }
    }
    echo "<p>Deleted $count cache files</p>";
} else {
    echo "<p style='color:orange;'>⚠️ Cache directory does not exist: $cachePath</p>";
    echo "<p>Creating directory...</p>";
    if (mkdir($cachePath, 0777, true)) {
        echo "<p style='color:green;'>✅ Created directory: $cachePath</p>";
        chmod($cachePath, 0777);
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory</p>";
    }
}

// Done
echo "<h2>Cache Clearing Complete</h2>";
echo "<p>Now try to refresh your admin page and reload it.</p>";
echo "<p>Your profile photo should now appear correctly at:</p>";
echo "<p><a href='/storage/profile-photos/1749726729-684ab60977cef.jpg?v=" . time() . "' target='_blank'>Direct Image Link (with cache busting)</a></p>";
echo "<p><a href='/admin/users/110007/edit?nocache=" . time() . "' target='_blank'>Edit User Page (with cache busting)</a></p>";
?> 