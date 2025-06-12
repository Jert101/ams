<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>User Model Debug</h1>";

// Define paths
$basePath = __DIR__;
$userModelPath = $basePath . '/app/Models/User.php';

// Read the User model
if (file_exists($userModelPath)) {
    echo "<h2>User Model Content</h2>";
    
    $userModelContent = file_get_contents($userModelPath);
    
    // Extract the getProfilePhotoUrlAttribute method
    if (preg_match('/public\s+function\s+getProfilePhotoUrlAttribute\s*\(.*?\)\s*\{.*?\}/s', $userModelContent, $matches)) {
        echo "<h3>getProfilePhotoUrlAttribute Method</h3>";
        echo "<pre style='background-color: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;'>";
        echo htmlspecialchars($matches[0]);
        echo "</pre>";
    } else {
        echo "<p style='color:red;'>❌ Could not find getProfilePhotoUrlAttribute method</p>";
    }
    
    // Extract the appends property
    if (preg_match('/protected\s+\$appends\s*=\s*\[(.*?)\]/s', $userModelContent, $matches)) {
        echo "<h3>Appends Property</h3>";
        echo "<pre style='background-color: #f5f5f5; padding: 10px;'>";
        echo htmlspecialchars($matches[0]);
        echo "</pre>";
        
        if (strpos($matches[1], 'profile_photo_url') !== false) {
            echo "<p style='color:green;'>✅ profile_photo_url is appended to the model</p>";
        } else {
            echo "<p style='color:red;'>❌ profile_photo_url is NOT appended to the model</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Could not find appends property</p>";
    }
} else {
    echo "<p style='color:red;'>❌ User model not found at $userModelPath</p>";
}

// Database connection parameters - update these with your actual credentials
$servername = "localhost";
$username = "if0_38972693"; // Your InfinityFree username
$password = ""; // You need to fill this with your actual database password
$dbname = "if0_38972693_ams"; // Your database name

// Check database connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection</h2>";
    echo "<p style='color:green;'>✅ Connected to database successfully</p>";
    
    // Get user with ID 110001
    $userId = 110001;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h2>User Information (ID: 110001)</h2>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        
        foreach ($user as $field => $value) {
            if ($field !== 'password' && $field !== 'remember_token') {
                echo "<tr><td>$field</td><td>" . ($value ?? '<em>NULL</em>') . "</td></tr>";
            }
        }
        
        echo "</table>";
        
        // Check profile photo path
        $photoPath = $user['profile_photo_path'] ?? null;
        echo "<h3>Profile Photo Path: " . ($photoPath ?? '<em>NULL</em>') . "</h3>";
        
        if ($photoPath) {
            // Define storage paths
            $storagePath = $basePath . '/storage';
            $storageAppPublicPath = $storagePath . '/app/public';
            $profilePhotosPath = $storageAppPublicPath . '/' . $photoPath;
            $publicPath = $basePath . '/public';
            $publicStoragePath = $publicPath . '/storage';
            $publicProfilePhotosPath = $publicStoragePath . '/' . $photoPath;
            
            // Check if file exists in storage
            $storageFileExists = file_exists($profilePhotosPath);
            echo "<p>Storage file exists: " . ($storageFileExists ? "✅ Yes" : "❌ No") . "</p>";
            echo "<p>Storage path: $profilePhotosPath</p>";
            
            // Check if file exists in public
            $publicFileExists = file_exists($publicProfilePhotosPath);
            echo "<p>Public file exists: " . ($publicFileExists ? "✅ Yes" : "❌ No") . "</p>";
            echo "<p>Public path: $publicProfilePhotosPath</p>";
            
            // Show preview
            echo "<h3>Profile Photo Preview</h3>";
            echo "<p>URL: /storage/$photoPath</p>";
            echo "<img src='/storage/$photoPath' style='max-width: 200px; border: 1px solid #ddd;'>";
            echo "<p>If the image doesn't appear, try <a href='/storage/$photoPath' target='_blank'>this direct link</a>.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ User with ID 110001 not found</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
}

// Create a mock implementation of the getProfilePhotoUrlAttribute method
echo "<h2>Mock Implementation Test</h2>";

function mockGetProfilePhotoUrl($profilePhotoPath, $basePath) {
    // Handle empty, null, or invalid profile photo paths
    if (empty($profilePhotoPath) || 
        $profilePhotoPath === '0' || 
        $profilePhotoPath === 0 || 
        $profilePhotoPath === 'null' ||
        $profilePhotoPath === 'NULL') {
        return '/kofa.png';
    }
    
    // Check if it's the default photo
    if ($profilePhotoPath === 'kofa.png') {
        return '/kofa.png';
    }
    
    // Check if it's a full URL
    if (filter_var($profilePhotoPath, FILTER_VALIDATE_URL)) {
        return $profilePhotoPath;
    }
    
    // Define paths
    $publicPath = $basePath . '/public';
    $publicStoragePath = $publicPath . '/storage';
    
    // IMPORTANT: First check if the file exists in public/storage directly
    $publicStorageFilePath = $publicStoragePath . '/' . $profilePhotoPath;
    if (file_exists($publicStorageFilePath)) {
        echo "<p>File exists at: $publicStorageFilePath</p>";
        return '/storage/' . $profilePhotoPath;
    }
    
    // Then check if it exists directly in public directory
    $publicDirectFilePath = $publicPath . '/' . $profilePhotoPath;
    if (file_exists($publicDirectFilePath)) {
        echo "<p>File exists at: $publicDirectFilePath</p>";
        return '/' . $profilePhotoPath;
    }
    
    echo "<p style='color:red;'>❌ File not found in any location</p>";
    echo "<p>Checked paths:</p>";
    echo "<ul>";
    echo "<li>$publicStorageFilePath</li>";
    echo "<li>$publicDirectFilePath</li>";
    echo "</ul>";
    
    return '/kofa.png';
}

if (isset($photoPath)) {
    echo "<p>Testing mock implementation with profile_photo_path: $photoPath</p>";
    $mockUrl = mockGetProfilePhotoUrl($photoPath, $basePath);
    echo "<p>Result URL: $mockUrl</p>";
    echo "<img src='$mockUrl' style='max-width: 200px; border: 1px solid #ddd;'>";
}

// Fix for profile photo issues
echo "<h2>Recommended Fixes</h2>";
echo "<ol>";
echo "<li>Ensure all directories exist with proper permissions</li>";
echo "<li>Make sure profile photos are copied to both storage and public directories</li>";
echo "<li>Verify that the User model has a proper getProfilePhotoUrlAttribute method</li>";
echo "<li>Clear browser cache to ensure you're seeing the latest version</li>";
echo "<li>Try uploading a new profile photo</li>";
echo "</ol>";

echo "<p>Try these scripts:</p>";
echo "<ul>";
echo "<li><a href='direct-photo-fix.php'>direct-photo-fix.php</a> - Creates a new profile photo and updates the database</li>";
echo "<li><a href='fix-profile-display.php'>fix-profile-display.php</a> - Diagnoses and fixes profile photo display issues</li>";
echo "<li><a href='verify-profile-photos.php'>verify-profile-photos.php</a> - Verifies and fixes profile photos for all users</li>";
echo "</ul>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 