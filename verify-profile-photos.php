<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Profile Photo Verification</h1>";

// Define paths
$basePath = __DIR__;
$storagePath = $basePath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicPath = $basePath . '/public';
$publicStoragePath = $publicPath . '/storage';
$publicProfilePhotosPath = $publicStoragePath . '/profile-photos';

// Database connection parameters - update these with your actual credentials
$servername = "localhost";
$username = "if0_38972693"; // Your InfinityFree username
$password = ""; // You need to fill this with your actual database password
$dbname = "if0_38972693_ams"; // Your database name

echo "<p>This script will verify all profile photos and fix any issues.</p>";
echo "<p>Please update the database password in this script before running it.</p>";

// Check if the script is being run with a specific action
$action = isset($_GET['action']) ? $_GET['action'] : '';
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$photoPath = isset($_GET['photo_path']) ? $_GET['photo_path'] : '';

// Process actions
if ($action === 'copy_to_public' && $photoPath) {
    $sourceFile = $profilePhotosPath . '/' . $photoPath;
    $destFile = $publicProfilePhotosPath . '/' . $photoPath;
    
    echo "<h2>Copying file to public directory</h2>";
    echo "<p>Source: $sourceFile</p>";
    echo "<p>Destination: $destFile</p>";
    
    // Ensure destination directory exists
    $destDir = dirname($destFile);
    if (!file_exists($destDir)) {
        if (mkdir($destDir, 0777, true)) {
            echo "<p style='color:green;'>✅ Created destination directory</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to create destination directory</p>";
        }
    }
    
    if (copy($sourceFile, $destFile)) {
        echo "<p style='color:green;'>✅ Successfully copied file</p>";
        chmod($destFile, 0644);
        echo "<p style='color:green;'>✅ Set file permissions to 0644</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to copy file</p>";
        $error = error_get_last();
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
    
    echo "<p><a href='verify-profile-photos.php'>Back to verification</a></p>";
    exit;
}

if ($action === 'copy_to_storage' && $photoPath) {
    $sourceFile = $publicProfilePhotosPath . '/' . $photoPath;
    $destFile = $profilePhotosPath . '/' . $photoPath;
    
    echo "<h2>Copying file to storage directory</h2>";
    echo "<p>Source: $sourceFile</p>";
    echo "<p>Destination: $destFile</p>";
    
    // Ensure destination directory exists
    $destDir = dirname($destFile);
    if (!file_exists($destDir)) {
        if (mkdir($destDir, 0777, true)) {
            echo "<p style='color:green;'>✅ Created destination directory</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to create destination directory</p>";
        }
    }
    
    if (copy($sourceFile, $destFile)) {
        echo "<p style='color:green;'>✅ Successfully copied file</p>";
        chmod($destFile, 0644);
        echo "<p style='color:green;'>✅ Set file permissions to 0644</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to copy file</p>";
        $error = error_get_last();
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
    
    echo "<p><a href='verify-profile-photos.php'>Back to verification</a></p>";
    exit;
}

if ($action === 'reset_photo' && $userId > 0) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = 'kofa.png' WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "<h2>Reset Profile Photo</h2>";
            echo "<p style='color:green;'>✅ Successfully reset profile photo for user ID: $userId</p>";
        } else {
            echo "<h2>Reset Profile Photo</h2>";
            echo "<p style='color:red;'>❌ Failed to reset profile photo for user ID: $userId</p>";
        }
    } catch(PDOException $e) {
        echo "<h2>Reset Profile Photo</h2>";
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
    
    echo "<p><a href='verify-profile-photos.php'>Back to verification</a></p>";
    exit;
}

// Check directory structure
echo "<h2>Directory Structure</h2>";
$directories = [
    'storage/app/public' => $storageAppPublicPath,
    'storage/app/public/profile-photos' => $profilePhotosPath,
    'public/storage' => $publicStoragePath,
    'public/storage/profile-photos' => $publicProfilePhotosPath,
];

foreach ($directories as $name => $path) {
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
    } else {
        echo "<p style='color:red;'>❌ Directory does not exist. Creating it...</p>";
        
        if (@mkdir($path, 0777, true)) {
            echo "<p style='color:green;'>✅ Successfully created directory</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to create directory</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
}

// Check profile photos in database
echo "<h2>Profile Photos in Database</h2>";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green;'>✅ Connected to database successfully</p>";
    
    // Query to get users with profile photos
    $stmt = $conn->prepare("SELECT id, user_id, name, email, profile_photo_path FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<p>Found " . count($users) . " users in the database</p>";
        
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Name</th><th>Email</th><th>Photo Path</th><th>Storage File</th><th>Public File</th><th>Preview</th><th>Actions</th></tr>";
        
        foreach ($users as $user) {
            $photoPath = $user['profile_photo_path'];
            $storageFilePath = $profilePhotosPath . '/' . $photoPath;
            $publicFilePath = $publicStoragePath . '/' . $photoPath;
            $photoUrl = '/storage/' . $photoPath;
            
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['user_id'] . "</td>";
            echo "<td>" . $user['name'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . ($photoPath ? $photoPath : '<em>None</em>') . "</td>";
            
            // Check if file exists in storage
            $storageFileExists = $photoPath && file_exists($storageFilePath);
            echo "<td>" . ($storageFileExists ? "✅ Exists" : "❌ Missing") . "</td>";
            
            // Check if file exists in public
            $publicFileExists = $photoPath && file_exists($publicFilePath);
            echo "<td>" . ($publicFileExists ? "✅ Exists" : "❌ Missing") . "</td>";
            
            // Preview
            echo "<td>";
            if ($photoPath) {
                echo "<img src='$photoUrl' style='max-width: 50px; max-height: 50px; border: 1px solid #ddd;'>";
            } else {
                echo "<em>No photo</em>";
            }
            echo "</td>";
            
            // Actions
            echo "<td>";
            if ($photoPath) {
                if ($storageFileExists && !$publicFileExists) {
                    echo "<a href='?action=copy_to_public&photo_path=" . urlencode($photoPath) . "'>Copy to Public</a><br>";
                }
                
                if (!$storageFileExists && $publicFileExists) {
                    echo "<a href='?action=copy_to_storage&photo_path=" . urlencode($photoPath) . "'>Copy to Storage</a><br>";
                }
                
                if (!$storageFileExists && !$publicFileExists) {
                    echo "File missing in both locations<br>";
                }
                
                echo "<a href='?action=reset_photo&user_id=" . $user['id'] . "' onclick='return confirm(\"Are you sure you want to reset this user's profile photo?\");'>Reset to Default</a>";
            } else {
                echo "<em>No actions needed</em>";
            }
            echo "</td>";
            
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No users found in the database</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
}

// Create a test profile photo
echo "<h2>Create Test Profile Photo</h2>";

echo "<p>Create a test profile photo to verify that the system is working correctly:</p>";

echo "<form method='post' action='?action=create_test'>";
echo "<button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Create Test Photo</button>";
echo "</form>";

if ($action === 'create_test') {
    $testPhotoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <circle cx="100" cy="100" r="100" fill="#f0f0f0"/>
  <circle cx="100" cy="80" r="40" fill="#999"/>
  <circle cx="100" cy="180" r="60" fill="#999"/>
</svg>
EOT;

    // Create test photo in both locations
    $testPhotoName = 'test-' . time() . '.svg';
    $storageTestPath = $profilePhotosPath . '/' . $testPhotoName;
    $publicTestPath = $publicProfilePhotosPath . '/' . $testPhotoName;

    if (file_put_contents($storageTestPath, $testPhotoContent)) {
        echo "<p style='color:green;'>✅ Created test photo in storage: $storageTestPath</p>";
        
        if (copy($storageTestPath, $publicTestPath)) {
            echo "<p style='color:green;'>✅ Copied test photo to public: $publicTestPath</p>";
            
            // Create a URL to the test photo
            $testPhotoUrl = '/storage/profile-photos/' . $testPhotoName;
            echo "<p>Test photo URL: <a href='$testPhotoUrl' target='_blank'>$testPhotoUrl</a></p>";
            echo "<p>Click the link to verify the file is accessible.</p>";
            
            // Display the test photo
            echo "<p>Test photo preview:</p>";
            echo "<img src='$testPhotoUrl' style='max-width: 200px; border: 1px solid #ddd;'>";
        } else {
            echo "<p style='color:red;'>❌ Failed to copy test photo to public</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to create test photo in storage</p>";
        $error = error_get_last();
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update the database password in this script</li>";
echo "<li>Check the table above to see if any profile photos are missing</li>";
echo "<li>Use the 'Copy to Public' or 'Copy to Storage' actions to fix missing files</li>";
echo "<li>Create a test photo to verify that the system is working correctly</li>";
echo "<li>Try uploading a new profile photo through the website</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 