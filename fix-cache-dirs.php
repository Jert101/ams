<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix Laravel Cache Directories</h1>";

// Define paths
$basePath = __DIR__;
$storagePath = $basePath . '/storage';
$bootstrapPath = $basePath . '/bootstrap';

// Create and verify storage framework directories
$frameworkDirs = [
    $storagePath . '/framework',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $bootstrapPath . '/cache',
];

// Ensure all directories exist and are writable
foreach ($frameworkDirs as $dir) {
    if (!file_exists($dir)) {
        echo "<p>Directory does not exist: $dir</p>";
        
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color:green;'>✅ Created directory: $dir</p>";
            chmod($dir, 0777);
            echo "<p>Set permissions to 0777</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to create directory: $dir</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ Directory exists: $dir</p>";
        
        // Ensure it's writable
        if (!is_writable($dir)) {
            echo "<p style='color:red;'>❌ Directory is not writable: $dir</p>";
            if (chmod($dir, 0777)) {
                echo "<p style='color:green;'>✅ Set permissions to 0777</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to set permissions</p>";
            }
        } else {
            echo "<p style='color:green;'>✅ Directory is writable</p>";
        }
    }
}

// Create a test file in each directory to verify write access
foreach ($frameworkDirs as $dir) {
    $testFile = $dir . '/test-' . time() . '.txt';
    if (file_put_contents($testFile, 'Test file to verify write access')) {
        echo "<p style='color:green;'>✅ Successfully wrote test file to: $dir</p>";
        // Remove the test file
        unlink($testFile);
    } else {
        echo "<p style='color:red;'>❌ Failed to write test file to: $dir</p>";
        $error = error_get_last();
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
}

// Done
echo "<h2>Cache Fix Complete</h2>";
echo "<p>Now try to refresh your admin page and upload a profile photo again.</p>";
echo "<p>You can also try viewing your profile photo directly at:</p>";
echo "<p><a href='/storage/profile-photos/1749726729-684ab60977cef.jpg' target='_blank'>/storage/profile-photos/1749726729-684ab60977cef.jpg</a></p>";
echo "<p><a href='/admin/users/110007/edit?nocache=" . time() . "' target='_blank'>Edit User Page (with cache busting)</a></p>";
?> 