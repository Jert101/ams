<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Clear All Cache</h1>";

// Define paths
$basePath = __DIR__;
$bootstrapCachePath = $basePath . '/bootstrap/cache';
$storageCachePath = $basePath . '/storage/framework/cache';
$storageViewsPath = $basePath . '/storage/framework/views';
$storageSessionsPath = $basePath . '/storage/framework/sessions';

echo "<h2>Clearing Bootstrap Cache</h2>";
$bootstrapCacheFiles = glob($bootstrapCachePath . '/*.php');
$count = 0;
foreach ($bootstrapCacheFiles as $file) {
    if (@unlink($file)) {
        echo "<p style='color:green;'>✅ Deleted: " . basename($file) . "</p>";
        $count++;
    } else {
        echo "<p style='color:red;'>❌ Failed to delete: " . basename($file) . "</p>";
    }
}
echo "<p>Deleted $count bootstrap cache files</p>";

echo "<h2>Clearing Framework Cache</h2>";
$frameworkCacheFiles = glob($storageCachePath . '/data/*');
$count = 0;
foreach ($frameworkCacheFiles as $file) {
    if (is_file($file) && @unlink($file)) {
        echo "<p style='color:green;'>✅ Deleted: " . basename($file) . "</p>";
        $count++;
    } else if (is_dir($file)) {
        echo "<p>Skipping directory: " . basename($file) . "</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to delete: " . basename($file) . "</p>";
    }
}
echo "<p>Deleted $count framework cache files</p>";

echo "<h2>Clearing Compiled Views</h2>";
$viewFiles = glob($storageViewsPath . '/*');
$count = 0;
foreach ($viewFiles as $file) {
    if (is_file($file) && @unlink($file)) {
        echo "<p style='color:green;'>✅ Deleted: " . basename($file) . "</p>";
        $count++;
    } else if (is_dir($file)) {
        echo "<p>Skipping directory: " . basename($file) . "</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to delete: " . basename($file) . "</p>";
    }
}
echo "<p>Deleted $count compiled view files</p>";

echo "<h2>Clearing Sessions</h2>";
$sessionFiles = glob($storageSessionsPath . '/*');
$count = 0;
foreach ($sessionFiles as $file) {
    if (is_file($file) && @unlink($file)) {
        echo "<p style='color:green;'>✅ Deleted: " . basename($file) . "</p>";
        $count++;
    } else if (is_dir($file)) {
        echo "<p>Skipping directory: " . basename($file) . "</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to delete: " . basename($file) . "</p>";
    }
}
echo "<p>Deleted $count session files</p>";

echo "<h2>Cache Directories</h2>";
$cacheDirectories = [
    'bootstrap/cache' => $bootstrapCachePath,
    'storage/framework/cache' => $storageCachePath,
    'storage/framework/views' => $storageViewsPath,
    'storage/framework/sessions' => $storageSessionsPath
];

foreach ($cacheDirectories as $name => $path) {
    echo "<h3>$name</h3>";
    
    if (file_exists($path)) {
        echo "<p style='color:green;'>✅ Directory exists</p>";
        
        // Check permissions
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<p>Permissions: $perms</p>";
        
        // Check if writable
        if (is_writable($path)) {
            echo "<p style='color:green;'>✅ Directory is writable</p>";
        } else {
            echo "<p style='color:red;'>❌ Directory is NOT writable</p>";
            
            // Try to fix permissions
            if (@chmod($path, 0777)) {
                echo "<p style='color:green;'>✅ Updated permissions to 0777</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to update permissions</p>";
            }
        }
        
        // List remaining files
        $files = scandir($path);
        $fileCount = 0;
        
        echo "<p>Remaining files:</p>";
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != "." && $file != ".." && is_file($path . '/' . $file)) {
                $fileCount++;
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
        
        if ($fileCount == 0) {
            echo "<p>No files remaining in this directory</p>";
        } else {
            echo "<p>Total files remaining: $fileCount</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Directory does not exist</p>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<p>All Laravel cache has been cleared. Try the following:</p>";
echo "<ol>";
echo "<li>Refresh your browser (or clear browser cache)</li>";
echo "<li>Try accessing the admin page with cache busting: <a href='/admin/users/110001/edit?nocache=" . time() . "' target='_blank'>Open admin edit page</a></li>";
echo "<li>Use the <a href='force-photo-fix.php'>force-photo-fix.php</a> script to update the profile photo</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 