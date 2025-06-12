<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Profile Photo Debug Tool</h1>";

// Database connection parameters - update these with your actual credentials
$servername = "localhost";
$username = "if0_38972693"; // Your InfinityFree username
$password = ""; // Your database password
$dbname = "if0_38972693_ams"; // Your database name

echo "<h2>Checking Database Connection</h2>";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green;'>Connected successfully to database</p>";
} catch(PDOException $e) {
    echo "<p style='color:red;'>Connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
    exit;
}

// Query to get users with profile photos
$stmt = $conn->prepare("SELECT id, name, email, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Users with Profile Photos</h2>";
if (count($users) == 0) {
    echo "<p>No users with profile photos found in database.</p>";
} else {
    echo "<p>Found " . count($users) . " users with profile photos.</p>";
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Photo Path</th><th>Storage File Exists</th><th>Public File Exists</th></tr>";
    
    foreach ($users as $user) {
        $storagePath = __DIR__ . "/storage/app/public/" . $user['profile_photo_path'];
        $publicPath = __DIR__ . "/public/storage/" . $user['profile_photo_path'];
        
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['profile_photo_path'] . "</td>";
        echo "<td>" . (file_exists($storagePath) ? "✅ Yes" : "❌ No") . "</td>";
        echo "<td>" . (file_exists($publicPath) ? "✅ Yes" : "❌ No") . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Check for users without profile photos
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE profile_photo_path IS NULL");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<p>Users without profile photos: " . $result['count'] . "</p>";

// Check directory structure
echo "<h2>Directory Structure Check</h2>";
$directories = [
    'storage/app/public/profile-photos',
    'public/storage/profile-photos'
];

foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    echo "<h3>$dir</h3>";
    
    if (file_exists($fullPath)) {
        echo "<p style='color:green;'>Directory exists</p>";
        echo "<p>Permissions: " . substr(sprintf('%o', fileperms($fullPath)), -4) . "</p>";
        echo "<p>Is writable: " . (is_writable($fullPath) ? "Yes" : "No") . "</p>";
        
        echo "<p>Contents:</p>";
        echo "<ul>";
        $files = scandir($fullPath);
        $hasFiles = false;
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file (" . filesize($fullPath . '/' . $file) . " bytes)</li>";
                $hasFiles = true;
            }
        }
        if (!$hasFiles) {
            echo "<li><em>No files</em></li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red;'>Directory does not exist</p>";
    }
}

// Check symlink status
echo "<h2>Symlink Check</h2>";
$symlink = __DIR__ . '/public/storage';
$target = __DIR__ . '/storage/app/public';

if (is_link($symlink)) {
    echo "<p style='color:green;'>Symlink exists from public/storage to storage/app/public</p>";
    echo "<p>Target: " . readlink($symlink) . "</p>";
} else {
    echo "<p style='color:red;'>No symlink found. Manual copy method is being used.</p>";
}

// Test creating a test file and copying it
echo "<h2>File Copy Test</h2>";
$testFile = __DIR__ . '/storage/app/public/profile-photos/test-' . time() . '.txt';
$testContent = "Test file created at " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "<p style='color:green;'>Successfully created test file in storage: $testFile</p>";
    
    $publicFile = __DIR__ . '/public/storage/profile-photos/test-' . time() . '.txt';
    if (copy($testFile, $publicFile)) {
        echo "<p style='color:green;'>Successfully copied file to public: $publicFile</p>";
    } else {
        echo "<p style='color:red;'>Failed to copy file to public location</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
} else {
    echo "<p style='color:red;'>Failed to create test file in storage</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Display the URL structure
echo "<h2>URL Structure</h2>";
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

echo "<p>Base URL: $baseUrl</p>";
echo "<p>Expected profile photo URL format: $baseUrl/storage/profile-photos/[filename]</p>";

// Check if storage directory is accessible via web
echo "<h2>Web Accessibility Test</h2>";
$testUrl = $baseUrl . '/storage/profile-photos/';
echo "<p>Testing access to: <a href='$testUrl' target='_blank'>$testUrl</a></p>";

echo "<p>Click the link above to check if the storage directory is accessible via web.</p>";

// Provide fix instructions
echo "<h2>Potential Fixes</h2>";
echo "<ol>";
echo "<li>Make sure the profile_photo_path in the database is correctly set (should be like 'profile-photos/filename.jpg')</li>";
echo "<li>Ensure both storage/app/public/profile-photos and public/storage/profile-photos directories exist and are writable</li>";
echo "<li>Check that files are being correctly copied from storage to public</li>";
echo "<li>Verify that the public/storage directory is accessible via web</li>";
echo "<li>Make sure .htaccess files allow access to the storage directory</li>";
echo "</ol>";

// Show the code that displays the profile photo
echo "<h2>Profile Photo Display Code</h2>";
echo "<p>The code that displays the profile photo typically looks like this:</p>";
echo "<pre>";
echo htmlspecialchars('<img src="{{ $user->profile_photo_url ?? asset(\'img/defaults/user.svg\') }}" alt="{{ $user->name }}\'s profile photo">');
echo "</pre>";

echo "<p>The profile_photo_url is typically generated from the profile_photo_path in the User model.</p>";
?> 