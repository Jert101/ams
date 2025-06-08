<?php
// Enable error reporting for diagnostics
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Output basic information
echo "<h1>Storage Diagnostic Tool</h1>";
echo "<h2>Server Information</h2>";
echo "<ul>";
echo "<li>PHP Version: " . PHP_VERSION . "</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "<li>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</li>";
echo "<li>Script Path: " . __FILE__ . "</li>";
echo "</ul>";

// Check directory existence and permissions
echo "<h2>Directory Status</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Directory</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Path</th></tr>";

$directories = [
    'storage' => __DIR__ . '/storage',
    'storage_app' => dirname(__DIR__) . '/storage/app',
    'storage_app_public' => dirname(__DIR__) . '/storage/app/public',
    'storage_public_profile_photos' => dirname(__DIR__) . '/storage/app/public/profile-photos',
    'bootstrap_cache' => dirname(__DIR__) . '/bootstrap/cache',
];

foreach ($directories as $name => $path) {
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    $writable = $exists ? is_writable($path) : false;
    $realpath = $exists ? realpath($path) : 'N/A';
    
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($readable ? '✅' : '❌') . "</td>";
    echo "<td>" . ($writable ? '✅' : '❌') . "</td>";
    echo "<td>$realpath</td>";
    echo "</tr>";
}
echo "</table>";

// Check if the storage symlink exists
echo "<h2>Storage Symlink Check</h2>";
$storageLink = __DIR__ . '/storage';
$storageTarget = dirname(__DIR__) . '/storage/app/public';

if (file_exists($storageLink)) {
    if (is_link($storageLink)) {
        $linkTarget = readlink($storageLink);
        echo "✅ Storage symlink exists and points to: $linkTarget<br>";
        
        if ($linkTarget == $storageTarget || realpath($linkTarget) == realpath($storageTarget)) {
            echo "✅ Symlink is correctly pointing to the storage/app/public directory<br>";
        } else {
            echo "❌ Symlink is pointing to the wrong location. Should point to: $storageTarget<br>";
        }
    } else {
        echo "❌ Storage exists but is not a symlink<br>";
    }
} else {
    echo "❌ Storage symlink does not exist<br>";
    echo "Consider running: <code>php artisan storage:link</code><br>";
}

// Test file upload capability
echo "<h2>File Upload Test</h2>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<input type='file' name='test_file'>";
echo "<input type='submit' name='submit' value='Test Upload'>";
echo "</form>";

if (isset($_POST['submit']) && isset($_FILES['test_file'])) {
    echo "<h3>Upload Results:</h3>";
    echo "<pre>";
    print_r($_FILES['test_file']);
    echo "</pre>";
    
    $uploadDir = dirname(__DIR__) . '/storage/app/public/test-uploads/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (mkdir($uploadDir, 0755, true)) {
            echo "Created test upload directory: $uploadDir<br>";
        } else {
            echo "Failed to create test upload directory: $uploadDir<br>";
        }
    }
    
    $uploadFile = $uploadDir . basename($_FILES['test_file']['name']);
    
    if (move_uploaded_file($_FILES['test_file']['tmp_name'], $uploadFile)) {
        echo "✅ File successfully uploaded to: $uploadFile<br>";
        
        // Check if the file is accessible via the symlink
        $publicUrl = 'storage/test-uploads/' . basename($_FILES['test_file']['name']);
        echo "Public URL should be: $publicUrl<br>";
        echo "Try accessing at: <a href='/$publicUrl' target='_blank'>/$publicUrl</a>";
    } else {
        echo "❌ Failed to upload file. Check permissions.<br>";
    }
}

// Environment configuration
echo "<h2>Environment Configuration</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

$envVars = [
    'APP_URL' => getenv('APP_URL'),
    'FILESYSTEM_DISK' => getenv('FILESYSTEM_DISK'),
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_file_uploads' => ini_get('max_file_uploads'),
];

foreach ($envVars as $name => $value) {
    echo "<tr><td>$name</td><td>$value</td></tr>";
}
echo "</table>";
?> 